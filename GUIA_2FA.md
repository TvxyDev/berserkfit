# Integração do Sistema 2FA - BerserkFit

## ✅ Arquivos Criados/Modificados

### 1. Novos Arquivos Criados
- ✅ `functions_2fa.php` - Funções auxiliares para 2FA
- ✅ `sql/add_2fa_field.sql` - Script SQL (opcional, já tens o campo)

### 2. Arquivos Modificados
- ✅ `setup_2fa.php` - Adaptado para BerserkFit (mysqli)
- ✅ `login_2fa.php` - Adaptado para BerserkFit (mysqli)
- ✅ `login.php` - Adicionada verificação de 2FA
- ✅ `perfil.php` - Adicionado botão para configurar 2FA

## 📋 Passos para Finalizar a Instalação

### Passo 1: Instalar Biblioteca 2FA via Composer

Abre o terminal no diretório do projeto e executa:

```bash
composer require robthree/twofactorauth
```

### Passo 2: Verificar Campo na Base de Dados

Como já tens o campo `tfa_secret` na tabela `user`, não precisas executar o script SQL.
Caso não tenhas, executa:

```sql
ALTER TABLE `user` ADD COLUMN `tfa_secret` VARCHAR(32) NULL DEFAULT NULL AFTER `username`;
```

### Passo 3: Testar o Sistema

1. **Acede ao perfil** (`perfil.php`)
2. Clica em **"Editar Perfil"**
3. Procura a secção **"Autenticação de Dois Fatores (2FA)"**
4. Clica em **"Configurar 2FA"**
5. Segue as instruções para ativar

## 🔐 Como Funciona

### Fluxo de Ativação (setup_2fa.php)

1. **Utilizador acede** a `setup_2fa.php` através do perfil
2. **Sistema gera** um QR Code com segredo único
3. **Utilizador escaneia** o QR Code com Google Authenticator/Authy
4. **Utilizador insere** o código de 6 dígitos para confirmar
5. **Sistema ativa** o 2FA e guarda o segredo na base de dados

### Fluxo de Login com 2FA

1. **Utilizador faz login** normal (email + senha)
2. **Sistema verifica** se tem `tfa_secret` na base de dados
3. **Se tiver 2FA ativo**: Redireciona para `login_2fa.php`
4. **Utilizador insere** o código de 6 dígitos do app
5. **Sistema valida** e completa o login

### Fluxo de Desativação

1. **Utilizador acede** a `setup_2fa.php`
2. Se 2FA estiver ativo, mostra botão **"Desativar 2FA"**
3. **Sistema remove** o `tfa_secret` da base de dados
4. 2FA desativado

## 🎨 Interface

### No Perfil (perfil.php)
- Adicionado um card destacado na secção de edição
- Ícone de escudo para segurança
- Botão direto para `setup_2fa.php`

### Página de Configuração (setup_2fa.php)
- Design moderno com gradientes
- QR Code centralizado
- Instruções passo a passo
- Botões de ativar/desativar

### Página de Login 2FA (login_2fa.php)
- Interface moderna com inputs individuais
- Suporta cola de código (paste)
- Animações e feedback visual
- Link para voltar ao login

## 🔧 Estrutura de Ficheiros

```
berserkfit-main/
├── functions_2fa.php          # Funções 2FA
├── setup_2fa.php               # Configurar 2FA
├── login_2fa.php               # Login com 2FA
├── login.php                   # ✏️ Modificado
├── perfil.php                  # ✏️ Modificado
├── ligacao.php                 # (existente)
├── vendor/                     # Composer (robthree/twofactorauth)
└── sql/
    └── add_2fa_field.sql       # Script SQL (opcional)
```

## 📱 Aplicações Recomendadas

Para os utilizadores usarem o 2FA, precisam de uma destas apps:
- **Google Authenticator** (iOS/Android)
- **Authy** (iOS/Android/Desktop)
- **Microsoft Authenticator** (iOS/Android)

## ⚠️ Notas Importantes

1. **Composer Required**: O projeto precisa da biblioteca `robthree/twofactorauth`
2. **Vendor Folder**: Certifica-te que a pasta `vendor/` existe
3. **Campo DB**: O campo `tfa_secret` deve existir na tabela `user`
4. **Session**: O sistema usa `$_SESSION` para guardar estados temporários

## 🐛 Troubleshooting

### Erro: "Class TwoFactorAuth not found"
**Solução**: Executa `composer require robthree/twofactorauth`

### QR Code não aparece
**Solução**: Verifica se a biblioteca foi instalada corretamente

### Erro ao guardar na BD
**Solução**: Verifica se o campo `tfa_secret` existe na tabela `user`

## 🎯 Próximos Passos Opcionais

- [ ] Adicionar códigos de recuperação (backup codes)
- [ ] Permitir múltiplos dispositivos 2FA
- [ ] Adicionar logs de tentativas de login
- [ ] Notificação por email quando 2FA é ativado/desativado
- [ ] Exigir 2FA para utilizadores Admin

---

**Desenvolvido para BerserkFit** 🏋️‍♂️
Qualquer dúvida, consulta este guia!
