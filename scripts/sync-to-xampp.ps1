param(
    [string]$SourcePath = (Resolve-Path "$PSScriptRoot\..").Path,
    [string]$DestinationPath = "C:\xampp\htdocs\Spring-2026-CPSC-332-Project"
)

if (-not (Test-Path $SourcePath)) {
    Write-Error "Source path does not exist: $SourcePath"
    exit 1
}

if (-not (Test-Path $DestinationPath)) {
    New-Item -ItemType Directory -Path $DestinationPath -Force | Out-Null
}

$excludeDirs = @(
    ".git",
    "node_modules"
)

$robocopyArgs = @(
    $SourcePath,
    $DestinationPath,
    "/MIR",
    "/XD"
) + $excludeDirs + @(
    "/R:1",
    "/W:1",
    "/NFL",
    "/NDL",
    "/NJH",
    "/NJS",
    "/NP"
)

robocopy @robocopyArgs | Out-Null
$exitCode = $LASTEXITCODE

# Robocopy exit codes 0-7 are success/non-fatal.
if ($exitCode -le 7) {
    Write-Host "Synced project to $DestinationPath"
    exit 0
}

Write-Error "Sync failed with robocopy exit code $exitCode"
exit $exitCode

