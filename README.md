<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sincronização da base de dados

- Defina as credenciais externas no `.env`: `DB_SANDBOX_*` e `DB_PRODUCTION_*` (host, port, database, username, password). Use `DB_SYNC_SOURCE` para escolher o perfil por omissão (`sandbox` ou `production`) e `DB_SYNC_TARGET_CONNECTION` se a ligação interna não for `mysql`.
- Para a aplicação em runtime, escolha a origem definindo `DB_CONNECTION` como `mysql_sandbox` (local) ou `mysql_production` (cloud). Ambas as ligações usam as mesmas credenciais definidas acima.
- O comando elimina todas as tabelas/views do destino e importa o dump externo via `mysqldump`/`mysql` (clientes MySQL precisam de estar no PATH).
- Sandbox: `php artisan db:sync-external sandbox` (ou sem argumento para usar `DB_SYNC_SOURCE`).
- Produção: `php artisan db:sync-external production --force` para evitar a confirmação interactiva em pipelines.
- Sem `--force` o comando pede confirmação antes de substituir a base interna.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Contratos dos condutores e boas-vindas

A funcionalidade está desativada por omissão (`DRIVER_ONBOARDING_ENABLED=false`),
preservando os fluxos existentes e permitindo publicar antes das migrações.
Ative apenas depois de validar a integração móvel e os textos aprovados.
Após executar `php artisan migrate` e definir `DRIVER_ONBOARDING_ENABLED=true`, a edição do condutor inclui geração manual de
contratos e envio de boas-vindas. A criação redireciona para a edição. Primeiro
configure os três modelos em **Templates e boas-vindas**. Os contratos começam
inativos, sem texto legal; só devem ser ativados depois de introduzir o texto
aprovado. O editor aceita texto simples e os campos indicados na página.

Cada geração conserva o texto, os dados usados e um SHA-256 da versão. Editar um
condutor ou template não modifica contratos gerados nem cria versões. Gerar outro
contrato substitui o anterior para efeitos de acesso, mantendo o seu histórico e
registo de aceitação. A aceitação exige uma sessão autenticada, consentimento
explícito e o identificador/hash da versão atual; guarda data, utilizador, IP e
user-agent. Este registo é de aceitação eletrónica; não integra um prestador de
assinatura digital qualificada. Os contratos antigos permanecem na área existente.

O e-mail usa a conta associada ao condutor, o assunto/texto do template e o mailer
configurado. `MAIL_MAILER=log` simula o envio localmente. Nenhum e-mail é enviado
automaticamente ao criar ou editar o condutor.

### Integração da aplicação móvel

O cliente móvel não está neste repositório. Tem de implementar a página obrigatória
de leitura e aceitação antes de disponibilizar esta funcionalidade na aplicação:

- O login passa a incluir `contract_acceptance_required` e `contract_url`.
- As rotas protegidas devolvem HTTP 423 e código `CONTRACT_ACCEPTANCE_REQUIRED`
  enquanto existir contrato pendente. O cliente deve encaminhar para a leitura.
- `GET /api/app/contract` devolve `id`, `version`, `title`, `body`, `content_hash`,
  `accepted_at` e `acceptance_text`.
- `POST /api/app/contract/accept` recebe `contract_id`, `content_hash`, `agree: true`.
  HTTP 409 exige voltar a obter e apresentar a versão atual.
- `GET /api/app/contract/pdf` descarrega o PDF com autenticação Bearer. O endpoint
  anterior `/api/app/contracts` aponta para este PDF quando há uma nova versão.
- O acesso anterior aos PDFs de relatórios mantém-se inalterado nesta publicação.

No site, o bloqueio e a página de aceitação já estão implementados. É sempre
possível terminar sessão. Os testes isolados podem ser executados com
`php vendor/bin/phpunit tests/Feature/DriverOnboardingTest.php`.
