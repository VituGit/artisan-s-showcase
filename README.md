# ARTISAN SHOWCASE

> Vitrine digital leve e responsiva para artesãos, focada em conversão via WhatsApp.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?logo=vue.js)](https://vuejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![Node.js](https://img.shields.io/badge/Node.js-18%2B-339933?logo=node.js)](https://nodejs.org/)

---

## 📋 Sumário

- [Visão Geral](#-visão-geral)
- [Recursos e Funcionalidades](#-recursos-e-funcionalidades)
- [Arquitetura & Tecnologias](#-arquitetura--tecnologias)
- [Começando](#-começando)
  - [Pré-requisitos](#pré-requisitos)
  - [Instalação](#instalação)
  - [Estrutura de Pastas](#estrutura-de-pastas)
- [Configuração](#-configuração)
- [Build & Deploy](#-build--deploy)
- [Qualidade](#-qualidade)
- [Roadmap](#-roadmap)
- [Contribuição](#-contribuição)
- [Licença](#-licença)
- [Créditos e Agradecimentos](#-créditos-e-agradecimentos)

---

## 🎯 Visão Geral

O **ARTISAN SHOWCASE** é uma plataforma web moderna desenvolvida para artesãos apresentarem seus produtos e serviços de forma profissional e atrativa. O projeto foi construído com foco em **conversão via WhatsApp**, oferecendo uma experiência otimizada para dispositivos móveis com design em tons naturais e marrons.

### 🎨 Motivação e Objetivos

- Criar uma vitrine digital acessível e profissional para artesãos
- Facilitar o contato direto com clientes através de CTAs (Call-to-Action) estratégicos
- Garantir performance excelente mesmo em conexões lentas
- Oferecer uma solução personalizável através de landing pages individuais por ateliê

### 📸 Screenshots

![Home](/docs/screen-home.png)
*Página inicial com apresentação do artesão e produtos em destaque*

![Catálogo](/docs/screen-catalog.png)
*Catálogo de produtos com categorias e lazy-loading de imagens*

### 📅 Informações do Projeto

- **Beneficiário**: Jonathas da Silva Fernandes
- **Período de Execução**: 20/08/2025 a 31/10/2025
- **Autor**: Victor Pereira Gurgel (RA 3882090404)

---

## ✨ Recursos e Funcionalidades

### 🏠 Seções da Página

- **Home**: Apresentação visual impactante com hero section
- **Sobre**: História e proposta do artesão/ateliê
- **Catálogo**: Produtos e serviços organizados por categorias
- **Depoimentos**: Avaliações de clientes
- **Contato**: Informações de contato e formulário integrado

### 🚀 Funcionalidades Técnicas

- ✅ **CTA WhatsApp**: Botões estratégicos para conversão direta
- ✅ **Lazy Loading**: Carregamento otimizado de imagens
- ✅ **Design Responsivo**: Experiência otimizada para mobile-first
- ✅ **Compressão de Imagens**: Assets otimizados para performance
- ✅ **SEO Básico**: Meta tags (title, description, Open Graph)
- ✅ **Landing Pages Personalizadas**: Cada ateliê possui sua própria LP configurável
- ✅ **Painel Administrativo**: Gerenciamento de produtos, categorias e configurações
- ✅ **Upload de Imagens**: Sistema de múltiplas fotos por produto
- ✅ **Paleta Customizável**: Cores personalizáveis por ateliê

---

## 🏗️ Arquitetura & Tecnologias

### Stack Principal

| Tecnologia | Versão | Propósito |
|------------|--------|-----------|
| **Laravel** | 11.x | Framework PHP para backend robusto e escalável |
| **Inertia.js** | 2.x | Bridge entre Laravel e Vue (SPA sem API REST) |
| **Vue 3** | 3.x | Framework JavaScript reativo para UI moderna |
| **TypeScript** | 5.x | Tipagem estática para código mais seguro |
| **Vite** | 5.x | Build tool rápido com HMR |
| **Tailwind CSS** | 3.x | Framework CSS utility-first |
| **shadcn/vue** | - | Componentes UI reutilizáveis e acessíveis |
| **MySQL** | 8.x | Banco de dados relacional |

### 📦 Por que essas tecnologias?

- **Laravel**: Ecossistema maduro, ORM Eloquent, migrações e seeders para gestão de dados
- **Inertia.js**: Elimina necessidade de API REST, mantendo benefícios de SPA
- **Vue 3 + TypeScript**: Componentes reativos com autocomplete e type safety
- **Vite**: Build extremamente rápido comparado ao Webpack
- **Tailwind CSS**: Desenvolvimento ágil sem CSS customizado excessivo
- **shadcn/vue**: Componentes prontos e personalizáveis seguindo padrões de acessibilidade

### 🗂️ Modelos de Dados

- `Atelier`: Ateliês/artesãos cadastrados
- `AtelierLpConfig`: Configurações de landing page (cores, textos, imagens)
- `Category`: Categorias de produtos
- `Product`: Produtos/serviços do artesão
- `ProductPhoto`: Fotos dos produtos (relação 1:N)
- `User`: Usuários do sistema (artesãos/admins)

---

## 🚀 Começando

### Pré-requisitos

Certifique-se de ter instalado:

- **PHP** >= 8.2 ([Download](https://www.php.net/downloads))
- **Composer** >= 2.x ([Download](https://getcomposer.org/))
- **Node.js** >= 18 LTS ([Download](https://nodejs.org/))
- **npm** ou **pnpm** (recomendado)
- **MySQL** >= 8.0 ou **PostgreSQL**
- **Git** para controle de versão

### Instalação

#### 1️⃣ Clone o repositório

```bash
git clone https://github.com/seu-usuario/artisan-showcase.git
cd artisan-showcase
```

#### 2️⃣ Instale dependências do backend

```bash
composer install
```

#### 3️⃣ Instale dependências do frontend

```bash
npm install
# ou com pnpm (recomendado para monorepos)
pnpm install
```

#### 4️⃣ Configure o ambiente

```bash
# Copie o arquivo de ambiente
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

Edite o arquivo `.env` com suas credenciais de banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artisan_showcase
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

#### 5️⃣ Execute as migrações e seeders

```bash
php artisan migrate --seed
```

#### 6️⃣ Crie link simbólico para storage

```bash
php artisan storage:link
```

#### 7️⃣ Inicie os servidores de desenvolvimento

**Terminal 1 - Backend (Laravel):**
```bash
php artisan serve
# Servidor rodando em http://localhost:8000
```

**Terminal 2 - Frontend (Vite):**
```bash
npm run dev
# Vite rodando em http://localhost:5173
```

Acesse **http://localhost:8000** no navegador.

---

### Estrutura de Pastas

```
artisan-showcase/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controladores da aplicação
│   │   ├── Middleware/        # Middlewares personalizados
│   │   └── Requests/          # Form Requests (validação)
│   ├── Models/                # Eloquent Models
│   │   ├── Atelier.php
│   │   ├── AtelierLpConfig.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── ProductPhoto.php
│   │   └── User.php
│   └── Providers/             # Service Providers
├── database/
│   ├── factories/             # Model Factories (dados fake)
│   ├── migrations/            # Migrações do banco de dados
│   └── seeders/               # Seeders (dados iniciais)
├── public/
│   ├── build/                 # Assets compilados (Vite)
│   └── storage/               # Link simbólico para storage/app/public
├── resources/
│   ├── css/
│   │   └── app.css            # Tailwind CSS base
│   ├── js/
│   │   ├── app.ts             # Entry point (CSR)
│   │   ├── ssr.ts             # Entry point (SSR - opcional)
│   │   ├── components/        # Componentes Vue reutilizáveis
│   │   ├── pages/             # Páginas Inertia (rotas)
│   │   ├── layouts/           # Layouts da aplicação
│   │   ├── types/             # Definições TypeScript
│   │   └── lib/               # Utilitários e helpers
│   └── views/                 # Views Blade (apenas root HTML)
├── routes/
│   ├── web.php                # Rotas web principais
│   ├── auth.php               # Rotas de autenticação
│   └── settings.php           # Rotas de configurações
├── storage/
│   ├── app/public/            # Arquivos públicos (uploads)
│   ├── framework/             # Cache, sessões, views compiladas
│   └── logs/                  # Logs da aplicação
├── tests/
│   ├── Feature/               # Testes de integração
│   └── Unit/                  # Testes unitários
├── .env.example               # Exemplo de variáveis de ambiente
├── artisan                    # CLI do Laravel
├── composer.json              # Dependências PHP
├── package.json               # Dependências Node.js
├── phpunit.xml                # Configuração PHPUnit
├── tsconfig.json              # Configuração TypeScript
└── vite.config.ts             # Configuração Vite
```

---

## ⚙️ Configuração

### Variáveis de Ambiente

O arquivo `.env` contém configurações sensíveis. Principais variáveis:

```env
# Aplicação
APP_NAME="Artisan Showcase"
APP_ENV=local
APP_KEY=                        # Gerado automaticamente
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artisan_showcase
DB_USERNAME=root
DB_PASSWORD=

# Filesystem (uploads)
FILESYSTEM_DISK=public

# Sessão e Cache
SESSION_DRIVER=file
CACHE_STORE=file

# Email (opcional - para funcionalidades futuras)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### Configuração de Storage

Para servir imagens de produtos, execute:

```bash
php artisan storage:link
```

Isso cria um link simbólico de `public/storage` para `storage/app/public`.

---

## 📦 Build & Deploy

### Scripts Disponíveis

| Comando | Descrição |
|---------|-----------|
| `npm run dev` | Inicia servidor de desenvolvimento Vite com HMR |
| `npm run build` | Compila assets para produção (minificação, tree-shaking) |
| `npm run preview` | Preview local da build de produção |
| `php artisan serve` | Inicia servidor Laravel (dev) |
| `php artisan test` | Executa testes com PHPUnit/Pest |

### Deploy em Produção

#### Passo 1: Preparação

```bash
# Instale dependências (sem dev)
composer install --optimize-autoloader --no-dev

# Compile assets
npm ci
npm run build

# Otimize configuração Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Passo 2: Deploy (Exemplo - Servidor VPS)

1. **Configure o servidor web** (Nginx/Apache) apontando para `public/index.php`
2. **Configure variáveis de ambiente** (`.env` em produção)
3. **Execute migrações**:
   ```bash
   php artisan migrate --force
   ```
4. **Configure permissões**:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

#### Deploy Automatizado

**Netlify / Vercel (Frontend estático - não aplicável)**  
Este projeto usa Laravel, necessita de servidor PHP. Considere:

- **Laravel Forge** ([forge.laravel.com](https://forge.laravel.com))
- **Ploi** ([ploi.io](https://ploi.io))
- **DigitalOcean App Platform**
- **AWS Elastic Beanstalk**
- **Heroku** (com buildpack PHP)

**Exemplo Heroku**:

```bash
# Instale Heroku CLI
heroku create artisan-showcase

# Configure buildpack PHP
heroku buildpacks:set heroku/php

# Configure variáveis de ambiente
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production

# Deploy
git push heroku main

# Execute migrações
heroku run php artisan migrate --force
```

---

## ✅ Qualidade

### Performance

**Meta: Lighthouse >= 90 em todas as categorias**

- **Performance**: 90+
- **Accessibility**: 95+
- **Best Practices**: 90+
- **SEO**: 95+

#### Otimizações Implementadas

- ✅ Lazy loading de imagens (`loading="lazy"`)
- ✅ Compressão de assets com Vite
- ✅ Minificação de CSS/JS
- ✅ Cache de configurações Laravel
- ✅ Compressão GZIP/Brotli (via servidor web)
- ✅ Preload de recursos críticos

### Acessibilidade

- Semântica HTML5 adequada
- Atributos ARIA onde necessário
- Contraste de cores WCAG AA
- Navegação por teclado
- Labels em formulários

### Linting e Formatação

**PHP** (Laravel Pint - integrado):
```bash
./vendor/bin/pint
```

**TypeScript/Vue** (ESLint - se configurado):
```bash
npm run lint
npm run lint:fix
```

**Prettier** (formatação):
```bash
npm run format
```

### Testes

```bash
# Testes unitários e de feature
php artisan test

# Com cobertura
php artisan test --coverage
```

---

## 🗺️ Roadmap

### 📌 Próximas Funcionalidades

- [ ] **Filtros no Catálogo**: Ordenação por preço, popularidade, categorias
- [ ] **Sistema de Busca**: Pesquisa por nome/descrição de produtos
- [ ] **Analytics de Cliques**: Rastreamento de conversões (CTAs WhatsApp)
- [ ] **Painel Simples de Gestão**: Dashboard para artesão editar produtos sem acesso ao admin
- [ ] **Multi-idiomas (i18n)**: Suporte para EN/ES além de PT-BR
- [ ] **PWA (Progressive Web App)**: Instalação no dispositivo móvel
- [ ] **Sistema de Avaliações**: Clientes avaliam produtos diretamente na LP
- [ ] **Integração com Redes Sociais**: Links e feeds do Instagram
- [ ] **Modo Escuro**: Alternância de tema claro/escuro
- [ ] **SEO Avançado**: Sitemap automático, structured data (JSON-LD)

### 🐛 Melhorias Técnicas

- [ ] Testes E2E com Playwright/Cypress
- [ ] CI/CD com GitHub Actions
- [ ] Docker Compose para ambiente de dev
- [ ] Logs estruturados (ELK Stack ou similar)
- [ ] Monitoramento de erros (Sentry)

---

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

### 1️⃣ Fork o projeto
### 2️⃣ Crie uma branch para sua feature

```bash
git checkout -b feature/minha-feature
```

### 3️⃣ Commit suas mudanças

Use commits semânticos (Conventional Commits):

```bash
git commit -m "feat: adiciona filtro por categoria no catálogo"
git commit -m "fix: corrige lazy loading em dispositivos iOS"
git commit -m "docs: atualiza README com instruções de deploy"
```

**Tipos de commit**:
- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Documentação
- `style`: Formatação (sem mudança de lógica)
- `refactor`: Refatoração de código
- `test`: Adiciona/modifica testes
- `chore`: Tarefas de manutenção

### 4️⃣ Push para sua branch

```bash
git push origin feature/minha-feature
```

### 5️⃣ Abra um Pull Request

Descreva claramente:
- O problema que resolve
- Como testar as mudanças
- Screenshots (se aplicável)

### 📋 Código de Conduta

- Seja respeitoso e colaborativo
- Reporte bugs com detalhes (passos para reproduzir)
- Sugira melhorias de forma construtiva

---

## 📄 Licença

Este projeto está sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

```
MIT License

Copyright (c) 2025 Victor Pereira Gurgel

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

[...texto completo da licença MIT...]
```

---

## 🙏 Créditos e Agradecimentos

### Beneficiário
- **Jonathas da Silva Fernandes** - Artesão beneficiário e inspiração para o projeto

### Desenvolvimento
- **Victor Pereira Gurgel** (RA 3882090404) - Desenvolvedor Full Stack

### Tecnologias e Recursos

- [Laravel](https://laravel.com) - Framework PHP
- [Vue.js](https://vuejs.org) - Framework JavaScript
- [Inertia.js](https://inertiajs.com) - Monolith moderno
- [Tailwind CSS](https://tailwindcss.com) - Framework CSS
- [shadcn/vue](https://www.shadcn-vue.com) - Componentes UI
- [Vite](https://vitejs.dev) - Build tool
- [TypeScript](https://www.typescriptlang.org) - Superset JavaScript

### Ícones e Imagens

- [Heroicons](https://heroicons.com) - Ícones SVG
- [Unsplash](https://unsplash.com) - Fotos de alta qualidade (quando aplicável)
- [Pixabay](https://pixabay.com) - Imagens livres de direitos autorais

### Fontes

- [Google Fonts](https://fonts.google.com) - Tipografia web
- Fonte principal: **(informar quando disponível)**

---

<div align="center">

**Desenvolvido com ❤️ para artesãos brasileiros**

⭐ Se este projeto foi útil, considere dar uma estrela no GitHub!

[⬆ Voltar ao topo](#artisan-showcase)

</div>
