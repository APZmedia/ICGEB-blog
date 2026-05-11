param(
    [string]$RepoRoot = "D:\ICGEB-blog",
    [string]$SyncRoot = "G:\Shared drives\CODING\MonitoringGeneDrives\public_html\wp-content",
    [string]$StagingAlias = "mgd-staging"
)

$ErrorActionPreference = "Stop"

$files = @(
    "plugins/doi-version-plugin/doi-version-plugin.php",
    "themes/icgeb-blog/functions.php",
    "themes/icgeb-blog/header.php",
    "themes/icgeb-blog/footer.php",
    "themes/icgeb-blog/index.php",
    "themes/icgeb-blog/search.php"
)

function Get-LocalHashMap([string]$basePath, [string[]]$relativeFiles) {
    $map = @{}
    foreach ($f in $relativeFiles) {
        $full = Join-Path $basePath $f
        if (-not (Test-Path -LiteralPath $full)) {
            $map[$f] = "MISSING"
            continue
        }
        $h = Get-FileHash -Algorithm SHA256 -LiteralPath $full
        $map[$f] = $h.Hash.ToLowerInvariant()
    }
    return $map
}

$repoHashes = Get-LocalHashMap -basePath $RepoRoot -relativeFiles $files

$syncRelative = $files | ForEach-Object { $_ }
$syncHashes = Get-LocalHashMap -basePath $SyncRoot -relativeFiles $syncRelative

$remoteCmd = "cd /home/customer/www/staging2.monitoringgenedrives.com/public_html/wp-content && sha256sum " + ($files -join " ")
$remoteOut = ssh $StagingAlias $remoteCmd

$stagingHashes = @{}
foreach ($line in $remoteOut) {
    if ($line -match "^([a-f0-9]{64})\s+(.+)$") {
        $stagingHashes[$matches[2]] = $matches[1]
    }
}

$hasMismatch = $false
foreach ($f in $files) {
    $repo = $repoHashes[$f]
    $sync = $syncHashes[$f]
    $stg = if ($stagingHashes.ContainsKey($f)) { $stagingHashes[$f] } else { "MISSING" }

    $okSync = ($repo -eq $sync)
    $okStg = ($repo -eq $stg)

    if (-not $okSync -or -not $okStg) {
        $hasMismatch = $true
    }

    "{0} | repo={1} | sync={2} | staging={3} | sync_ok={4} | staging_ok={5}" -f $f, $repo, $sync, $stg, $okSync, $okStg
}

if ($hasMismatch) {
    throw "Verification failed: repo/sync/staging are not aligned."
}

"Verification passed: repo, sync folder, and staging are aligned."
