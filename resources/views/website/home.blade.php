@extends('layouts.website')

@section('content')
<header class="hero-modern">
    <div class="container py-5">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7 text-white">
                <span class="hero-kicker">Mundo TVDE</span>
                <h1 class="display-5 fw-bold mt-3 mb-3">{{ $hero->title ?? 'Conduz o teu futuro com uma marca que acelera contigo.' }}</h1>
                <p class="lead text-white-50 mb-4">{{ $hero->subtitle ?? 'Soluções completas para motoristas e operadores TVDE, com acompanhamento próximo e foco em resultados.' }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary btn-lg" href="{{ $hero->link ?? '/tvde/aluguer-de-viaturas' }}">{{ $hero->button ?? 'Começar agora' }}</a>
                    <button class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#chatModal">
                        <i class="bi bi-stars me-1"></i>Pergunte ao assistente virtual
                    </button>
                </div>
                <div class="row row-cols-2 row-cols-md-3 g-3 mt-4">
                    <div class="col">
                        <div class="mini-stat">
                            <h5>24/7</h5>
                            <small>Suporte dedicado</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mini-stat">
                            <h5>+200</h5>
                            <small>Motoristas ativos</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mini-stat">
                            <h5>4.9★</h5>
                            <small>Satisfação da equipa</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-panel p-4 p-lg-5">
                    <p class="text-uppercase text-primary fw-bold mb-2">Operação profissional</p>
                    <h3 class="fw-bold mb-3">Tudo o que precisa para trabalhar em TVDE</h3>
                    <ul class="list-unstyled m-0">
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>Onboarding e acompanhamento dedicado</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>Consultoria para maior rentabilidade</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i>Formação, stand e suporte contínuo</li>
                        <li><i class="bi bi-check2-circle text-primary me-2"></i>Atendimento por WhatsApp e assistente virtual</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="clean-block section-soft">
    <div class="container">
        <div class="block-heading">
            <span class="section-kicker">Destaques</span>
            <h2 class="fw-bold">{{ $info->title ?? 'Porque escolher a Mundo TVDE' }}</h2>
            <p class="text-secondary">{{ $info->description ?? 'Uma estrutura completa para quem quer entrar e crescer no setor TVDE.' }}</p>
        </div>
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                @if ($info && $info->image)
                    <img class="img-fluid rounded-4 shadow-sm" src="{{ $info->image->url }}" alt="{{ $info->title ?? 'Mundo TVDE' }}">
                @endif
            </div>
            <div class="col-lg-6">
                <div class="service-card">
                    {!! $info->text ?? '<p>Atualize este conteúdo no painel para destacar os benefícios principais da empresa.</p>' !!}
                    @if ($info && $info->button && $info->link)
                        <a class="btn btn-primary mt-3" href="{{ $info->link }}">{{ $info->button }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="clean-block">
    <div class="container">
        <div class="block-heading">
            <span class="section-kicker">Serviços</span>
            <h2 class="fw-bold">Atividades e soluções em destaque</h2>
        </div>
        <div class="row g-4">
            @foreach ($activities as $activity)
                <div class="col-md-6 col-lg-4">
                    <article class="service-card">
                        <i class="{{ $activity->icon ?: 'bi bi-briefcase' }}"></i>
                        <h5 class="fw-bold">{{ $activity->title }}</h5>
                        <p class="text-secondary">{{ $activity->description }}</p>
                        @if ($activity->button && $activity->link)
                            <a class="btn btn-outline-primary btn-sm" href="{{ $activity->link }}">{{ $activity->button }}</a>
                        @endif
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="articles" class="clean-block section-soft">
    <div class="container">
        <div class="block-heading">
            <span class="section-kicker">Notícias</span>
            <h2 class="fw-bold">Atualidade TVDE</h2>
            <p class="text-secondary">Saiba as últimas novidades do setor</p>
        </div>
        <div class="row g-4">
            @foreach (App\Models\Article::where('active', true)->orderBy('created_at', 'desc')->limit(3)->get() as $article)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        @if ($article->photo)
                            <img src="{{ $article->photo->getUrl() }}" class="card-img-top" alt="{{ $article->title ?? 'Notícia' }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $article->title ?? '' }}</h5>
                            <p class="card-text text-secondary">{{ $article->resume ?? '' }}</p>
                            <a href="/noticia/{{ $article->id }}/{{ Illuminate\Support\Str::slug($article->title) }}" class="btn btn-primary mt-auto">Ler notícia</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="clean-block">
    <div class="container">
        <div class="block-heading">
            <span class="section-kicker">Testemunhos</span>
            <h2 class="fw-bold">O que dizem sobre nós</h2>
        </div>
        <div class="row g-4">
            @foreach ($testimonials as $testimonial)
                <div class="col-md-6 col-lg-4">
                    <div class="clean-testimonial-item h-100 p-4">
                        <p class="text-secondary mb-3">{{ $testimonial->message }}</p>
                        <h5 class="fw-bold mb-0">{{ $testimonial->name }}</h5>
                        <small class="text-muted">{{ $testimonial->job_position }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatModalLabel">Assistente Virtual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="emailSection">
                    <p class="mb-2">Antes de começarmos, introduza o seu email:</p>
                    <input type="email" class="form-control mb-3" id="emailInput" placeholder="exemplo@email.com" />
                    <button class="btn btn-primary" id="startChat">Iniciar conversa</button>
                </div>

                <div id="chatSection" style="display: none;">
                    <div id="chatBox" class="chat-box"></div>
                    <div class="input-group mt-3">
                        <input type="text" id="userInput" class="form-control" placeholder="Escreva a sua pergunta..." />
                        <button class="btn btn-primary d-flex align-items-center gap-2" id="sendMessage">
                            <span class="spinner-border spinner-border-sm d-none" id="chatSpinner" role="status" aria-hidden="true"></span>
                            <span id="sendText">Enviar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let conversation = [];
    let currentEmail = '';

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function renderConversation() {
        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML = '';
        conversation.forEach(msg => {
            const className = msg.role === 'assistant' ? 'assistant-message' : 'user-message';
            chatBox.innerHTML += `<div class="${className}">${msg.content}</div>`;
        });
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    document.getElementById('startChat').addEventListener('click', function () {
        const email = document.getElementById('emailInput').value.trim();
        if (!validateEmail(email)) {
            alert('Por favor, insira um email válido.');
            return;
        }

        currentEmail = email;

        fetch("{{ url('/api/website-messages') }}/" + encodeURIComponent(email))
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    conversation = data;
                    renderConversation();
                }
                document.getElementById('emailSection').style.display = 'none';
                document.getElementById('chatSection').style.display = 'block';
            })
            .catch(() => {
                document.getElementById('emailSection').style.display = 'none';
                document.getElementById('chatSection').style.display = 'block';
            });
    });

    function sendUserMessage() {
        const input = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendMessage');
        const spinner = document.getElementById('chatSpinner');
        const sendText = document.getElementById('sendText');

        const message = input.value.trim();
        if (message === '') return;

        input.disabled = true;
        sendBtn.disabled = true;
        spinner.classList.remove('d-none');
        sendText.textContent = 'A responder...';

        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML += `<div class="user-message">${message}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
        input.value = '';

        conversation.push({ role: 'user', content: message });
        if (conversation.length > 10) conversation.shift();

        fetch("{{ route('assistente.virtual') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: currentEmail, conversation })
        })
            .then(res => res.json())
            .then(data => {
                if (data.reply) {
                    conversation.push({ role: 'assistant', content: data.reply });
                    if (conversation.length > 10) conversation.shift();
                    chatBox.innerHTML += `<div class="assistant-message">${data.reply}</div>`;
                    chatBox.scrollTop = chatBox.scrollHeight;
                } else {
                    chatBox.innerHTML += '<div class="assistant-message">Ocorreu um erro ao obter resposta.</div>';
                }
            })
            .catch(() => {
                chatBox.innerHTML += '<div class="assistant-message">Erro ao comunicar com o assistente.</div>';
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                spinner.classList.add('d-none');
                sendText.textContent = 'Enviar';
                input.focus();
            });
    }

    document.getElementById('sendMessage').addEventListener('click', sendUserMessage);
    document.getElementById('userInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendUserMessage();
        }
    });
</script>
@endsection