# FreelaFlow

> Plataforma SaaS de gestão para freelancers e micro-agências — clientes, projetos, propostas, contratos, faturamento, tempo e relatórios em um único lugar.

O **FreelaFlow** nasceu para resolver o caos operacional do profissional autônomo: planilhas soltas, propostas em DOCX, controle de horas na cabeça e faturas que nunca sabemos se foram pagas. Tudo isso vive numa aplicação web coesa, com uma experiência moderna e 100% em português.

---

## Índice

- [Stack tecnológica](#stack-tecnológica)
- [Diferenciais](#diferenciais)
- [Funcionalidades](#funcionalidades)
- [Arquitetura](#arquitetura)
- [Como rodar o projeto](#como-rodar-o-projeto)
- [Ambiente de demonstração](#ambiente-de-demonstração)
- [Estrutura de diretórios](#estrutura-de-diretórios)
- [Testes](#testes)
- [Decisões técnicas](#decisões-técnicas)

---

## Stack tecnológica

| Camada        | Tecnologia                                                                 |
|---------------|----------------------------------------------------------------------------|
| Linguagem     | PHP 8.2+                                                                    |
| Framework     | Laravel 11                                                                  |
| Frontend      | Livewire 3 (com **Volt**) + Alpine.js + Tailwind CSS 3                      |
| Banco de dados| SQLite (pronto para PostgreSQL/MySQL via env)                               |
| Build         | Vite                                                                        |
| Autenticação  | Laravel Breeze (stack Volt)                                                 |
| Qualidade     | Pint (formatação), PHPUnit (testes)                                         |

A escolha do **TALL stack** (Tailwind, Alpine, Livewire, Laravel) entrega uma SPA-like experience sem a complexidade de um bundle separado em React/Vue: o estado vivo da UI vive no servidor, o que reduz drasticamente a superfície de bugs de sincronização de estado no cliente.

---

## Diferenciais

- **Multi-tenant por isolamento de dados.** Todo dado de negócio pertence a um `user_id` e é filtrado automaticamente por um `GlobalUserScope`. Um usuário jamais enxerga os dados de outro — sem `where` espalhado pela aplicação.
- **Páginas públicas com token seguro.** Propostas, contratos e faturas podem ser compartilhados com o cliente via URL assinada (`/p/proposal/{id}/{token}`). Sem o token correto → `404`. O cliente visualiza um documento limpo, sem acessar o sistema.
- **Onboarding guiado de 5 passos.** O primeiro acesso conduz o freelancer a configurar empresa, moeda, tipo de atuação, primeiro cliente e primeiro projeto — eliminando a "tela em branco" que mata a retenção.
- **Relatórios e dashboards com gráficos nativos.** Receita vs. despesas, margem de lucro, faturas por status, despesas por categoria, tarefas por status e horas registradas — tudo renderizado com componentes de gráfico próprios (sem dependência de JS pesada).
- **Domínio rico e coeso.** Mais de 20 modelos (clientes com contatos, projetos com membros, tarefas com comentários, time tracking, etc.) com regras de negócio centralizadas em Services/Actions.
- **Componentes reutilizáveis e design system próprio.** `input`, `select`, `button`, `modal`, `stat-card`, `status-badge`, `charts/*` — consistência visual garantida por tokens (preto fosco + laranja `#FF6B00`).
- **Ações em tempo real.** Criação, edição e exclusão de registros acontecem via Livewire, com toasts e modais, sem reload de página.
- **Pronto para produção.** Policies de autorização, validação em camadas, seeder idempotente e suíte de testes.

---

## Funcionalidades

### Clientes (`/clients`)
Cadastro de clientes com múltiplos contatos (`ClientContact`). Visualização detalhada com projetos, propostas e faturas relacionadas.

### Projetos (`/projects`)
Gestão de projetos com status, orçamento, progresso e membros da equipe (`ProjectMember`). Kanban de tarefas por projeto.

### Tarefas (`/tasks`)
Quadro **Kanban** (arrastar e soltar) + visão em lista. Prioridades (baixa → urgente), vencimentos com realce de atraso, filtros e ordenação.

### Propostas (`/proposals`)
Criação de propostas comerciais com itens (descrição, quantidade, preço), desconto/impostos e total calculado em tempo real. Envio de **link público** para o cliente.

### Contratos (`/contracts`)
Geração de contratos a partir de propostas aceitas, com página pública assinável.

### Faturamento
- **Faturas** (`/invoices`): emissão, status (rascunho, enviada, paga, vencida, cancelada), itens e link público de cobrança.
- **Pagamentos** (`/payments`): registro de recebimentos vinculados a faturas.
- **Despesas** (`/expenses`): categorizadas (`ExpenseCategory`) com TIPI/centro de custo.
- **Financeiro** (`/finance`): visão consolidada de contas a pagar/receber.

### Controle de tempo (`/time`)
Registro de apontamentos (`TimeEntry`) por tarefa/projeto, base para faturamento por hora.

### Agenda (`/calendar`)
Calendário mensal com eventos (reuniões, prazos, entregas, tarefas), cores por tipo, horários, botão "Hoje" e painel de próximos eventos.

### Arquivos (`/files`)
Upload e download de documentos por cliente/projeto, com controle de acesso.

### Relatórios (`/reports`)
Períodos (hoje, semana, mês, ano, personalizado) com:
- KPIs: receita, despesa, lucro e **margem**.
- Gráfico de **receita vs. despesas** (6 meses).
- **Faturas por status**, **despesas por categoria**, **tarefas por status** e **horas registradas**.
- Ranking de **top clientes por receita**.
- Exportação **CSV**.

### Notificações (`/notifications`)
Central de notificações do usuário (vencimentos, propostas aceitas, etc.).

### Configurações (`/settings`)
Perfil, moeda, preferências de notificação e segurança da conta.

### Dashboard (`/dashboard`)
Visão executiva: KPIs do mês, gráfico de receita/despesa/lucro, projetos por status, receita por cliente, tarefas por status, próximos vencimentos e tarefas a vencer.

---

## Arquitetura

```
app/
├── Livewire/            # Componentes da UI (páginas e widgets)
│   ├── Actions/         # Ações reutilizáveis (ex.: autenticação)
│   ├── Concerns/        # Traits (ex.: SearchableTable)
│   └── Forms/           # Form objects (ex.: LoginForm)
├── Models/              # 20+ modelos de domínio
│   └── Concerns/        # BelongsToUser (relacionamento + escopo)
├── Services/            # FinancialService (agregações de negócio)
├── Policies/            # Autorização por recurso
├── Http/Middleware/     # EnsureOnboarded
└── helpers.php          # status_label, money, event_type_meta, etc.

resources/views/
├── livewire/            # Views dos componentes
├── components/          # Design system (input, modal, charts, ...)
└── layouts/             # guest + auth (split-screen brand)
```

**Princípios aplicados:**

1. **Escopo global de tenant** (`UserScope` + trait `BelongsToUser`): isolamento transparente de dados.
2. **Lógica de negócio em Services/Actions**, não nas views — views ficam com apresentação.
3. **Policies** para autorização fina, desacopladas do controller/componente.
4. **Componentes Blade reutilizáveis** com tokens de design, evitando duplicação de markup.
5. **Helpers puras** (`status_label`, `money`, `priority_*`, `event_type_meta`) para manter views limpas e i18n-ready.

---

## Como rodar o projeto

```bash
# 1. Instalar dependências
composer install
npm install

# 2. Ambiente
cp .env.example .env
php artisan key:generate

# 3. Banco (SQLite por padrão)
touch database/database.sqlite
php artisan migrate --seed

# 4. Build dos assets e servidor
npm run build
php artisan serve
```

Ambiente de desenvolvimento com hot-reload:

```bash
composer run dev   # roda server + vite + logs simultaneamente
```

---

## Ambiente de demonstração

A aplicação já vem com um botão **"Entrar como demo"** na tela de login, que cria (ou reutiliza) um usuário de demonstração já onboardado:

```
E-mail: demo@freelaflow.com
Senha:  password
```

O `DemoSeeder` popula o banco com dados realistas (clientes, projetos, tarefas, propostas, faturas, pagamentos, despesas, apontamentos e eventos) e é **idempotente** — pode ser rodado múltiplas vezes sem duplicar registros.

---

## Estrutura de diretórios (resumo)

| Diretório                 | Responsabilidade                              |
|---------------------------|----------------------------------------------|
| `app/Livewire`            | Componentes de página e widgets interativos  |
| `app/Models`              | Modelos de domínio + escopos de tenant       |
| `app/Services`            | Agregações financeiras e regras de negócio   |
| `app/Policies`            | Autorização por recurso                       |
| `resources/views/components` | Design system e gráficos                   |
| `routes/web.php`          | Definição de rotas autenticadas e públicas   |

---

## Testes

A suíte foca em comportamento crítico de ponta a ponta:

```bash
php artisan test
```

Cobertura atual (`tests/Feature/SmokeTest.php`):
- Renderização das páginas autenticadas (200).
- Renderização das páginas públicas via token.
- Ação de login demo.
- **Isolamento de dados** entre usuários (multi-tenant).
- Rejeição de páginas públicas com token inválido.

---

## Decisões técnicas

- **Livewire + Volt** no lugar de um SPA em JS: menor complexidade de sincronização de estado, melhor DX e time-to-market mais rápido para CRUDs ricos.
- **Token-based public pages**: compartilhamento seguro com clientes sem criar contas para terceiros.
- **GlobalUserScope**: segurança por padrão — o filtro de tenant é aplicado em todo `select`, inclusive em relacionamentos e agregações.
- **SQLite como padrão**: zero-config para demo/dev; trocável por PostgreSQL/MySQL via `.env` sem mudança de código.
- **Design system próprio em Blade**: consistência visual e manutenção centralizada em tokens (`#0A0A0A` / `#FF6B00`).

---

© FreelaFlow — construído com Laravel, Livewire e muito café.
