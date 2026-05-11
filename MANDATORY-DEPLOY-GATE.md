# Mandatory Deploy Gate (Repo -> Sync -> Staging)

Run this gate on **every deploy step**.

## Command

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\verify-phase3-sync.ps1
```

## Mandatory Rule

- If the script reports any mismatch, **stop immediately**.
- Do not proceed to next step until repo, sync folder, and staging hashes match.

## Required Sequence

1. Commit in repo.
2. Sync to local staging folder.
3. Deploy to staging via SSH.
4. Run `verify-phase3-sync.ps1`.
5. Validate live HTML markers (Schema + alt text).
6. Only then proceed to production.
