<#
.SYNOPSIS
    Creates the initial Git commit and publishes the BGP Sessions widget repository to GitHub.

.DESCRIPTION
    Run this script from the repository root on Windows 11 PowerShell.
    It assumes Git and GitHub CLI are installed and that 'gh auth login' was already completed.

    Execute com cuidado: ele cria o repositório no GitHub e envia o primeiro commit.
#>

param(
    [string]$RepositoryName = "zabbix-bgp-sessions-widget",
    [string]$Description = "Custom Zabbix 7.0 dashboard widget for visual BGP session monitoring with automatic Juniper BGP item discovery.",
    [switch]$Private
)

$ErrorActionPreference = "Stop"

# Select repository visibility.
$visibility = if ($Private) { "--private" } else { "--public" }

# Initialize local Git repository.
git init
git branch -M main

# Add project files.
git add .
git commit -m "Initial release of BGP Sessions widget"

# Create GitHub repository and push.
gh repo create $RepositoryName `
  --description $Description `
  $visibility `
  --source . `
  --remote origin `
  --push

# Add recommended repository topics.
gh repo edit --add-topic zabbix,zabbix-widget,bgp,juniper,monitoring,snmp,network-monitoring

# Create and push the first version tag.
git tag -a v0.3.4 -m "BGP Sessions widget v0.3.4"
git push origin v0.3.4

Write-Host "Repository published successfully." -ForegroundColor Green
