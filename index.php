<!DOCTYPE html>
<html lang="pt-PT">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BerserkFit</title>
  <link rel="stylesheet" href="css/estilo.css" />
  <link rel="stylesheet" href="css/footer.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Inter&display=swap" rel="stylesheet" />
</head>

<body>
  <header>
    <nav>
      <div class="logotipo">
        <img src="assets/logotipo1.png" alt="Logótipo BerserkFit" />
      </div>
      <button class="menu-toggle" id="menuToggle" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>
      <div class="menu-overlay" id="menuOverlay"></div>
      <ul class="nav-menu" id="navMenu">
        <li><a href="#inicio">Início</a></li>
        <li><a href="#funcionalidades">Funcionalidades</a></li>
        <li><a href="#planos">Planos</a></li>
        <li><a href="#sobre">Sobre</a></li>
        <li><a href="#depoimentos">Testemunhos</a></li>
        <li><a href="#contato">Contacto</a></li>
        <li class="botao-login-mobile"><a href="login.php" class="botao-login">Entrar</a></li>
      </ul>
      <a href="login.php" class="botao-login botao-login-desktop">Entrar</a>
    </nav>
  </header>

  <main>
    <section id="inicio" class="hero-section1">
      <div class="heroi-container">
        <div class="heroi-texto">
          <h1 class="fade-in-element">BerserkFit</h1>
          <p class="subtitulo-heroi fade-in-element">
            "A app de treino que acompanha a tua evolução, planeia os teus treinos
            e mantém a tua disciplina no ginásio."
          </p>
          <ul class="heroi-lista fade-in-element">
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" />
              Acompanhamento do progresso
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Planeador de
              treinos
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Disciplina e
              motivação
            </li>
          </ul>
          <a href="login.php" class="botao-heroi fade-in-element">Começar Agora</a>
        </div>
        <div class="heroi-imagem fade-in-element">
          <img src="assets/pessoa.png" alt="Pessoa a exercitar-se" />
        </div>
      </div>
    </section>

    <section id="funcionalidades" class="fade-in-element">
      <h2>Um Arsenal Para a Tua Evolução</h2>
      <p class="subtitulo">
        Ferramentas inteligentes para um treino disciplinado e motivador.
      </p>
      <div class="grade-funcionalidades">
        <div class="cartao fade-in-element">
          <div class="cartao-icone">
            <img src="assets/checklist.svg" alt="Ícone Checklist" />
          </div>
          <h3>Checklist Diária</h3>
          <p>
            Cria e personaliza a tua checklist diária para forjar disciplina e
            acompanhar o teu progresso.
          </p>
        </div>
        <div class="cartao fade-in-element">
          <div class="cartao-icone">
            <img src="assets/bot.svg" alt="Ícone ChatBot IA" />
          </div>
          <h3>Gerador de Treinos IA</h3>
          <p>
            Obtém treinos personalizados com o nosso chatbot, adaptados ao teu
            nível e equipamento.
          </p>
        </div>
        <div class="cartao fade-in-element">
          <div class="cartao-icone">
            <img src="assets/bell.svg" alt="Ícone de Sino de Notificação" />
          </div>
          <h3>Notificações Motivacionais</h3>
          <p>
            Recebe lembretes e mensagens de encorajamento para manter o foco
            nos teus objetivos.
          </p>
        </div>
      </div>
    </section>

    <section id="planos" class="fade-in-element">
      <h2>Planos para Guerreiros</h2>
      <p class="subtitulo">
        Escolhe o arsenal que te levará à vitória. Começa grátis.
      </p>
      <div class="grade-precos">
        <div class="cartao-preco fade-in-element">
          <h3>Spartan</h3>
          <p class="preco">Grátis</p>
          <p class="periodo-preco">Para sempre</p>
          <ul>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Gerador de
              treinos básico
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Checklist
              diária para 5 tarefas
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Acesso à
              comunidade
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Exportação de
              treinos
            </li>
          </ul>
          <button>Começa Grátis</button>
        </div>
        <div class="cartao-preco destacado fade-in-element">
          <h3>Gladiator</h3>
          <p class="preco">€19,90<span class="preco-mes">/mês</span></p>
          <ul>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Tudo do plano
              Spartan
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Gerador de
              treinos com IA avançada
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Histórico de
              consistência completo
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Checklist
              diária ilimitada
            </li>
          </ul>
          <button class="botao-destacado">Sê um Gladiador</button>
        </div>
        <div class="cartao-preco fade-in-element">
          <h3>Berserker</h3>
          <p class="preco">€39,90<span class="preco-mes">/mês</span></p>
          <ul>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Tudo do plano
              Gladiator
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Notificações
              motivacionais personalizadas
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Suporte
              prioritário via chat
            </li>
            <li>
              <img src="assets/checkmark.svg" alt="Checkmark" /> Acesso
              antecipado a novas funcionalidades
            </li>
          </ul>
          <button>Liberta o Berserker</button>
        </div>
      </div>
    </section>

    <section id="sobre" class="fade-in-element">
      <div class="sobre-container">
        <div class="missao fade-in-element">
          <h2>A Nossa Missão</h2>
          <p>
            Na BerserkFit, acreditamos que a verdadeira força nasce da
            disciplina. A nossa missão é fornecer as ferramentas para que
            construas não apenas um físico poderoso, mas uma mente resiliente
            como a de um guerreiro espartano.
          </p>
          <p>
            Combinamos treino inteligente a motivação implacável para que
            possas superar os teus limites e alcançar a evolução contínua.
            Junta-te a nós nesta jornada de autotransformação.
          </p>
        </div>
        <div class="legiao fade-in-element">
          <h2>A Nossa Legião</h2>
          <div class="grade-legiao">
            <div class="membro-legiao fade-in-element">
              <img
                src="https://image.thum.io/get/auth/61665-3592a5b65f5a4aeaa535035f3755b40d/width/300/https://raw.githubusercontent.com/miguelsmuller/dev-berserkfit/main/assets/lykos.png"
                alt="Retrato de Lykos, o fundador do BerserkFit" />
              <h3>Victor Santos</h3>
              <p>Fundador &<br />Desenvolvedor</p>
           </div>
        </div>
      </div>
    </section>

    <section id="depoimentos" class="fade-in-element">
      <h2>O Que Dizem Os Nossos Guerreiros</h2>
      <div class="grade-depoimentos">
        <div class="cartao-depoimento fade-in-element">
          <img src="https://i.pravatar.cc/100?u=1" alt="Avatar de um utilizador" />
          <p>
            "O BerserkFit mudou o meu jogo. A disciplina que eu precisava, na
            palma da minha mão."
          </p>
          <h3>Marcus</h3>
          <span>Gladiador</span>
        </div>
        <div class="cartao-depoimento fade-in-element">
          <img src="https://i.pravatar.cc/100?u=2" alt="Avatar de um utilizador" />
          <p>
            "Finalmente uma app que entende a mentalidade de um atleta. Os
            treinos são insanos!"
          </p>
          <h3>Helena</h3>
          <span>Berserker</span>
        </div>
        <div class="cartao-depoimento fade-in-element">
          <img src="https://i.pravatar.cc/100?u=3" alt="Avatar de um utilizador" />
          <p>
            "Comecei com o plano Spartan e já sinto a diferença. A comunidade
            é um grande apoio."
          </p>
          <h3>Gael</h3>
          <span>Spartan</span>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="footer-container">
      <div class="footer-column">
        <h3>BerserkFit</h3>
        <p>
          Subscreve a nossa newsletter para te manteres atualizado sobre
          funcionalidades e lançamentos.
        </p>
        <form class="newsletter-form">
          <input type="email" placeholder="Insere o teu e-mail" />
          <button type="submit">Subscrever</button>
        </form>
        <p class="newsletter-disclaimer">
          Ao subscreveres, concordas com a nossa Política de Privacidade e
          consentes em receber atualizações da nossa empresa.
        </p>
      </div>
      <div class="footer-column">
        <h4>Navegação</h4>
        <ul>
          <li><a href="#inicio">Início</a></li>
          <li><a href="#funcionalidades">Funcionalidades</a></li>
          <li><a href="#planos">Planos</a></li>
          <li><a href="#sobre">Sobre</a></li>
          <li><a href="#depoimentos">Testemunhos</a></li>
          <li><a href="#contato">Contacto</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h4>Segue-nos</h4>
        <div class="social-icons">
          <a href="#"><img src="assets/facebook.svg" alt="Facebook" /> Facebook</a>
          <a href="#"><img src="assets/instagram.svg" alt="Instagram" /> Instagram</a>
          <a href="#"><img src="assets/twitter.svg" alt="Twitter" /> Twitter</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2024 BerserkFit. Todos os direitos reservados.</p>
      <div class="footer-legal">
        <a href="#">Política de Privacidade</a>
        <a href="#">Termos de Serviço</a>
        <a href="#">Definições de Cookies</a>
      </div>
    </div>
  </footer>

  <script src="js/main.js"></script>
</body>

</html>