<footer class="site-footer pt-5 pb-0">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <h5>Área de utilizador</h5>
                <ul>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Registo</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h5>Mundo TVDE</h5>
                <ul>
                    @foreach (App\Models\Page::all() as $page)
                        <li>
                            <a href="/pagina/{{ $page->id }}/{{ Illuminate\Support\Str::slug($page->title, '-') }}">{{ $page->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h5>Apoio</h5>
                <ul>
                    <li><a href="/faqs">FAQ</a></li>
                    <li><a href="https://api.whatsapp.com/send?phone=351962366777" target="_blank" rel="noopener noreferrer">WhatsApp Equipa</a></li>
                    <li><a href="https://api.whatsapp.com/send?phone=351926978477" target="_blank" rel="noopener noreferrer">WhatsApp Assistente</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h5>Legal</h5>
                <ul>
                    @foreach (\App\Models\Legal::all() as $legal)
                        <li>
                            <a href="/legal/{{ $legal->id }}/{{ Illuminate\Support\Str::slug($legal->title, '-') }}">{{ $legal->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom mt-4 py-3">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>© {{ date('Y') }} Mundo TVDE</span>
            <span>Mobilidade e soluções profissionais TVDE</span>
        </div>
    </div>
</footer>

<div class="floating-actions" aria-label="Ações rápidas">
    <a class="wa-ai" href="https://api.whatsapp.com/send?phone=351926978477" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Assistente">
        <i class="bi bi-stars"></i><span class="label">Assistente</span>
    </a>
    <a class="wa-main" href="https://api.whatsapp.com/send?phone=351962366777" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Equipa">
        <i class="bi bi-whatsapp"></i><span class="label">WhatsApp</span>
    </a>
    <div class="social-row">
        <a class="social" target="_blank" href="https://www.facebook.com/mundotvde" rel="noopener noreferrer" aria-label="Facebook Mundo TVDE">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
        <a class="social" target="_blank" href="https://www.instagram.com/mundotvde/" rel="noopener noreferrer" aria-label="Instagram Mundo TVDE">
            <i class="fa-brands fa-instagram"></i>
        </a>
    </div>
</div>

