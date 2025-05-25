@extends('layouts.website')
@section('content')
<section class="clean-block clean-hero" style="background-image: url({{ $hero->image->url }});color: rgba(5, 79, 119, 0.50);">
    <div class="text">
        <h2>{{ $hero->title }}</h2>
        <p>{{ $hero->subtitle }}</p>
        <a class="btn btn-outline-light btn-lg" href="{{ $hero->link }}">{{ $hero->button }}</a>
        <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#chatModal" style="margin-left: 10px;">Pergunte ao assistente virtual</button>

    </div>
</section>
<section class="clean-block clean-info dark">
    <div class="container">
        <div class="block-heading">
            <h2 class="text-info">{{ $info->title }}</h2>
            <p>{{ $info->description }}</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6"><img class="img-thumbnail" src="{{ $info->image->url }}">
            </div>
            <div class="col-md-6">
                {!! $info->text !!}
                <a class="btn btn-outline-primary btn-lg" href="{{ $info->link }}">{{ $info->button }}</a>
            </div>
        </div>
    </div>
</section>

<section id="articles" style="padding: 50px 0">

    <div class="container">
        <div class="block-heading text-center">
            <h2 class="text-info">Notícias</h2>
            <p>Saiba as últimas novidades TVDE</p>
        </div>
        <div class="row align-items-center">
            @foreach (App\Models\Article::where('active', true)->orderBy('created_at', 'desc')->limit('3')->get() as $article)
            <div class="col-md-4">
                <div class="card" style="margin: 20px;">
                    @if ($article->photo)
                    <img src="{{ $article->photo->getUrl() }}" class="card-img-top" alt="...">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $article->title ?? '' }}</h5>
                        <p class="card-text">{{ $article->resume ?? '' }}</p>
                        <a href="/noticia/{{ $article->id }}/{{ Str::slug($article->title) }}" class="btn btn-primary">Ler notícia</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</section>

<section class="clean-block features" style="background: var(--bs-gray-900);color: rgb(255,255,255);">
    <div class="container">
        <div class="block-heading">
            <h2 class="text-info">Atividades</h2>
        </div>
        <div class="row justify-content-center">
            @foreach ($activities as $activity)
            <div class="col-md-5 feature-box"><i class="{{ $activity->icon }}"></i>
                <h4>{{ $activity->title }}</h4>
                <p>{{ $activity->description }}</p><a class="btn btn-primary btn-sm" role="button" href="{{ $activity->link }}">{{ $activity->button }}</a>
            </div>
            @endforeach
            <div class="col-md-5 feature-box"></div>
        </div>
    </div>
</section>
<section class="clean-block clean-testimonials dark">
    <div class="container">
        <div class="block-heading">
            <h2 class="text-info">Testemunhos</h2>
        </div>
        <div class="row">
            @foreach ($testimonials as $testimonial)
            <div class="col-lg-4">
                <div class="card clean-testimonial-item border-0 rounded-0">
                    <div class="card-body">
                        <p class="card-text">{{ $testimonial->message }}</p>
                        <h3>{{ $testimonial->name }}</h3>
                        <h4 class="card-title">{{ $testimonial->job_position }}</h4>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Modal do Assistente Virtual -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assistente Virtual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Etapa 1: Email -->
                <div id="emailSection">
                    <p>Antes de começarmos, por favor introduza o seu email:</p>
                    <input type="email" class="form-control mb-3" id="emailInput" placeholder="exemplo@email.com" />
                    <button class="btn btn-primary" id="startChat">Iniciar Conversa</button>
                </div>

                <!-- Etapa 2: Chat -->
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
@section('styles')
<style>
    #sendMessage:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .chat-box {
        max-height: 400px;
        overflow-y: auto;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .user-message {
        align-self: flex-end;
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 10px 15px;
        border-radius: 15px 15px 0 15px;
        max-width: 75%;
        word-wrap: break-word;
    }

    .assistant-message {
        align-self: flex-start;
        background-color: #f8d7da;
        color: #842029;
        padding: 10px 15px;
        border-radius: 15px 15px 15px 0;
        max-width: 75%;
        word-wrap: break-word;
    }

</style>

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

    document.getElementById('startChat').addEventListener('click', function() {
        const email = document.getElementById('emailInput').value.trim();
        if (!validateEmail(email)) {
            alert('Por favor, insira um email válido.');
            return;
        }

        currentEmail = email;

        // Buscar mensagens anteriores, se existirem
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
                // mesmo que falhe, avança
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

        // Bloquear input e botão
        input.disabled = true;
        sendBtn.disabled = true;
        spinner.classList.remove('d-none');
        sendText.textContent = 'A responder...';

        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML += `<div class="user-message">${message}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
        input.value = '';

        conversation.push({
            role: 'user'
            , content: message
        });
        if (conversation.length > 10) conversation.shift();

        fetch("{{ route('assistente.virtual') }}", {
                method: 'POST'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                , }
                , body: JSON.stringify({
                    email: currentEmail
                    , conversation
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.reply) {
                    conversation.push({
                        role: 'assistant'
                        , content: data.reply
                    });
                    if (conversation.length > 10) conversation.shift();

                    chatBox.innerHTML += `<div class="assistant-message">${data.reply}</div>`;
                    chatBox.scrollTop = chatBox.scrollHeight;
                } else {
                    chatBox.innerHTML += `<div class="assistant-message">Ocorreu um erro ao obter resposta.</div>`;
                }
            })
            .catch(() => {
                chatBox.innerHTML += `<div class="assistant-message">Erro ao comunicar com o assistente.</div>`;
            })
            .finally(() => {
                // Reativar input e botão
                input.disabled = false;
                sendBtn.disabled = false;
                spinner.classList.add('d-none');
                sendText.textContent = 'Enviar';
                input.focus();
            });
    }


    document.getElementById('sendMessage').addEventListener('click', sendUserMessage);

    document.getElementById('userInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendUserMessage();
        }
    });

</script>


@endsection
