# Initial GitHub Publishing from Windows 11 / Publicação Inicial no GitHub via Windows 11

[English](#english) | [Português do Brasil](#português-do-brasil)

---

## English

This guide shows how to create the GitHub repository and push the first version of the **BGP Sessions** widget using Windows 11 and PowerShell.

### Suggested repository information

```text
Repository name: zabbix-bgp-sessions-widget
Description: Custom Zabbix 7.0 dashboard widget for visual BGP session monitoring with automatic Juniper BGP item discovery.
Topics: zabbix, zabbix-widget, bgp, juniper, monitoring, snmp, network-monitoring
Visibility: public or private, according to your preference
```

### 1. Install Git and GitHub CLI

Open PowerShell as a normal user or as Administrator.

```powershell
# Installs Git for Windows.
winget install --id Git.Git -e

# Installs GitHub CLI, used to create the repository from the command line.
winget install --id GitHub.cli -e
```

Close and reopen PowerShell after installation so the commands become available in the PATH.

Check the installation:

```powershell
git --version
gh --version
```

### 2. Authenticate GitHub CLI

```powershell
# Follow the interactive browser login flow.
gh auth login
```

Recommended answers:

```text
GitHub.com
HTTPS
Login with a web browser
```

### 3. Create the local project folder

Adjust the path if you prefer another location.

```powershell
# Creates a local workspace folder.
mkdir C:\Projetos

# Enters the workspace folder.
cd C:\Projetos

# Creates the repository folder.
mkdir zabbix-bgp-sessions-widget

# Enters the repository folder.
cd zabbix-bgp-sessions-widget
```

### 4. Copy the project files

Copy all files from this prepared project folder into:

```text
C:\Projetos\zabbix-bgp-sessions-widget
```

The final folder should contain:

```text
bgp_sessions\
README.md
INSTALL.md
CHANGELOG.md
GITHUB_SETUP_WINDOWS11.md
.gitignore
```

### 5. Configure Git identity

Use your real name and GitHub e-mail address.

```powershell
git config --global user.name "Karim Mansur"
git config --global user.email "your-email@example.com"
```

### 6. Create the first local commit

```powershell
# Initializes a new Git repository in the current folder.
git init

# Defines the default branch name as main.
git branch -M main

# Shows the current files that Git can track.
git status

# Adds all project files to the first commit.
git add .

# Creates the initial commit.
git commit -m "Initial release of BGP Sessions widget"
```

### 7. Create the GitHub repository and push

Using GitHub CLI:

```powershell
# Creates the GitHub repository and pushes the current local repository.
gh repo create zabbix-bgp-sessions-widget `
  --description "Custom Zabbix 7.0 dashboard widget for visual BGP session monitoring with automatic Juniper BGP item discovery." `
  --public `
  --source . `
  --remote origin `
  --push
```

For a private repository, replace `--public` with:

```powershell
--private
```

### 8. Add repository topics

```powershell
gh repo edit --add-topic zabbix,zabbix-widget,bgp,juniper,monitoring,snmp,network-monitoring
```

### 9. Create the first version tag

```powershell
# Creates a local annotated tag for the current module version.
git tag -a v0.3.4 -m "BGP Sessions widget v0.3.4"

# Pushes the tag to GitHub.
git push origin v0.3.4
```

---

## Português do Brasil

Este guia mostra como criar o repositório no GitHub e enviar a primeira versão do widget **BGP Sessions** usando Windows 11 e PowerShell.

### Informações sugeridas do repositório

```text
Nome do repositório: zabbix-bgp-sessions-widget
Descrição: Custom Zabbix 7.0 dashboard widget for visual BGP session monitoring with automatic Juniper BGP item discovery.
Tópicos: zabbix, zabbix-widget, bgp, juniper, monitoring, snmp, network-monitoring
Visibilidade: público ou privado, conforme sua preferência
```

### 1. Instalar Git e GitHub CLI

Abra o PowerShell como usuário normal ou como Administrador.

```powershell
# Instala o Git for Windows.
winget install --id Git.Git -e

# Instala o GitHub CLI, usado para criar o repositório pela linha de comando.
winget install --id GitHub.cli -e
```

Feche e abra novamente o PowerShell após a instalação para garantir que os comandos estejam disponíveis no PATH.

Verifique a instalação:

```powershell
git --version
gh --version
```

### 2. Autenticar o GitHub CLI

```powershell
# Siga o fluxo interativo de login pelo navegador.
gh auth login
```

Respostas recomendadas:

```text
GitHub.com
HTTPS
Login with a web browser
```

### 3. Criar a pasta local do projeto

Ajuste o caminho se preferir outro local.

```powershell
# Cria uma pasta local para projetos.
mkdir C:\Projetos

# Entra na pasta de projetos.
cd C:\Projetos

# Cria a pasta do repositório.
mkdir zabbix-bgp-sessions-widget

# Entra na pasta do repositório.
cd zabbix-bgp-sessions-widget
```

### 4. Copiar os arquivos do projeto

Copie todos os arquivos desta pasta de projeto preparada para:

```text
C:\Projetos\zabbix-bgp-sessions-widget
```

A pasta final deve conter:

```text
bgp_sessions\
README.md
INSTALL.md
CHANGELOG.md
GITHUB_SETUP_WINDOWS11.md
.gitignore
```

### 5. Configurar a identidade do Git

Use seu nome real e o e-mail vinculado ao GitHub.

```powershell
git config --global user.name "Karim Mansur"
git config --global user.email "seu-email@example.com"
```

### 6. Criar o primeiro commit local

```powershell
# Inicializa um novo repositório Git na pasta atual.
git init

# Define o nome da branch principal como main.
git branch -M main

# Mostra os arquivos que o Git pode controlar.
git status

# Adiciona todos os arquivos do projeto ao primeiro commit.
git add .

# Cria o commit inicial.
git commit -m "Initial release of BGP Sessions widget"
```

### 7. Criar o repositório no GitHub e fazer o push

Usando GitHub CLI:

```powershell
# Cria o repositório no GitHub e envia o repositório local atual.
gh repo create zabbix-bgp-sessions-widget `
  --description "Custom Zabbix 7.0 dashboard widget for visual BGP session monitoring with automatic Juniper BGP item discovery." `
  --public `
  --source . `
  --remote origin `
  --push
```

Para criar como repositório privado, substitua `--public` por:

```powershell
--private
```

### 8. Adicionar tópicos ao repositório

```powershell
gh repo edit --add-topic zabbix,zabbix-widget,bgp,juniper,monitoring,snmp,network-monitoring
```

### 9. Criar a primeira tag de versão

```powershell
# Cria uma tag anotada local para a versão atual do módulo.
git tag -a v0.3.4 -m "BGP Sessions widget v0.3.4"

# Envia a tag para o GitHub.
git push origin v0.3.4
```
