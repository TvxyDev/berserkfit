# BerserkFit - Plataforma Web de Treino Personalizado com Inteligência Artificial

**Autor:** Victor Santos (N.º 25, 3.º TIS)
**Curso:** Curso Profissional de Técnico de Informática - Sistemas
**Escola:** Escola Secundária com 3.º ciclo
**Ano Letivo:** 2025/2026

---

## Agradecimentos

Gostaria de começar por agradecer a todos os que contribuíram, direta ou indiretamente, para a realização deste projeto de Prova de Aptidão Profissional (PAP).

Agradeço primeiramente aos meus professores e orientadores, pelo acompanhamento constante, pela partilha de conhecimentos técnicos fundamentais e pela orientação prestada ao longo de todo o ciclo de formação (2023/2026). As suas críticas construtivas e sugestões foram essenciais para o aprimoramento da plataforma BerserkFit.

Aos meus colegas de turma, agradeço o espírito de entreajuda e companheirismo demonstrado durante os três anos de curso, bem como a disponibilidade para testar a aplicação e fornecer feedback valioso.

Por fim, um agradecimento especial à minha família e amigos, pelo apoio incondicional e pela paciência demonstrada durante as muitas horas de dedicação que este projeto exigiu.

---

## Acrónimos, Abreviaturas e Siglas

*   **API:** Application Programming Interface
*   **CSS:** Cascading Style Sheets
*   **CRUD:** Create, Read, Update, Delete
*   **HTML:** HyperText Markup Language
*   **IA:** Inteligência Artificial
*   **JS:** JavaScript
*   **JSON:** JavaScript Object Notation
*   **MVC:** Model-View-Controller
*   **PHP:** Hypertext Preprocessor
*   **SQL:** Structured Query Language
*   **UI/UX:** User Interface / User Experience
*   **PT:** Personal Trainer

---

## Resumo

O presente relatório descreve o desenvolvimento do **BerserkFit**, um website inovador dedicado ao fitness e bem-estar, criado no âmbito da Prova de Aptidão Profissional. O projeto surge como resposta à dificuldade comum que muitos praticantes de ginásio, especialmente iniciantes, sentem em manter a motivação e aceder a planos de treino estruturados sem custos proibitivos.

Diferenciando-se das soluções tradicionais, o BerserkFit integra tecnologias web robustas (PHP, MySQL) com a vanguarda da Inteligência Artificial Generativa (API Google Gemini). A plataforma oferece um "Personal Trainer" virtual disponível 24/7, capaz de dialogar com o utilizador e gerar planos de treino personalizados em tempo real. Além disso, o sistema incorpora fortes elementos de gamificação — como sistema de ligas (desde "Renegado" a "Berserker"), contagem de "streaks" (dias consecutivos) e conquistas — para combater a desistência.

O resultado final é uma aplicação web funcional, segura e esteticamente apurada, que permite aos utilizadores gerir a sua evolução física, controlar hábitos diários (água, sono, alimentação) e receber motivação constante, cumprindo assim o objetivo de democratizar o acesso a um treino de qualidade.

**Palavras-chave:** Web Development, Inteligência Artificial, Fitness, Gamificação, PHP, MySQL.

---

## Introdução

### Motivação e Justificativa
A escolha deste tema não foi aleatória. Como praticante de ginásio, senti na primeira pessoa as dificuldades inerentes ao início da atividade física: a falta de orientação, a monotonia dos treinos genéricos e, acima de tudo, a dificuldade em manter a disciplina a longo prazo. Percebi que se tivesse acesso a uma ferramenta que não só organizasse os treinos, mas que também fornecesse feedback e motivação diária, o meu percurso teria sido significativamente mais fácil.

O BerserkFit nasce dessa necessidade pessoal, transformada num projeto tecnológico com potencial para ajudar uma comunidade mais vasta. Numa sociedade cada vez mais sedentária, ferramentas que promovam o exercício físico de forma engajadora e acessível são de extrema relevância social e pública.

### Objetivos do Projeto
O objetivo central foi desenvolver uma plataforma web completa que atuasse como um companheiro digital de treino. Para tal, foram definidos os seguintes objetivos específicos:

1.  **Desenvolvimento de um Ecossistema Web Completo:** Criar um website responsivo e intuitivo, acessível tanto em computadores como em dispositivos móveis, para que o utilizador possa levar o seu treino para o ginásio.
2.  **Integração de Inteligência Artificial:** Implementar um Chatbot inteligente, alimentado pela API Gemini da Google, capaz de compreender linguagem natural, responder a dúvidas sobre fitness e criar rotinas de treino adaptadas ao nível e equipamento do utilizador.
3.  **Sistema de Gamificação e Retenção:** Desenvolver mecanismos que recompensem a consistência, incluindo um sistema de "Streaks" (fogo diário) e um sistema de ranking com Ligas baseadas na mitologia nórdica/espartana.
4.  **Gestão de Dados e Progresso:** Permitir o registo detalhado de métricas de saúde (peso, água ingerida, calorias) e o histórico de treinos realizados, visualizáveis através de gráficos no Dashboard.
5.  **Segurança e Autenticação:** Garantir a proteção dos dados dos utilizadores através de encriptação e oferecer métodos de login modernos, como a autenticação via Google.

### Metodologia de Trabalho
O projeto seguiu uma metodologia de desenvolvimento iterativa e incremental. Inicialmente, procedeu-se ao levantamento de requisitos e à análise de plataformas concorrentes (benchmarking), como o MyFitnessPal e o Strong. Em seguida, foi feito o desenho da base de dados e dos protótipos de interface (mockups).

A fase de codificação foi dividida em módulos: primeiro a estrutura base e autenticação, depois o núcleo de gestão de treinos, e finalmente a integração complexa com a API de IA e a camada de gamificação. Foram utilizados softwares como o **Visual Studio Code** para a escrita de código, **XAMPP** para a simulação do servidor local (Apache/MySQL), e **DrawSQL** para a modelação da base de dados. O **GitHub** foi fundamental para o controlo de versões e backup do projeto.

---

## Desenvolvimento do Projeto

### 1. Identidade Visual e UI/UX
A identidade visual do BerserkFit foi cuidadosamente planeada para transmitir força, intensidade e modernidade. O nome funde "Berserk" (guerreiros nórdicos de fúria incontrolável) com "Fit", sugerindo uma abordagem intensa e dedicada ao treino.

*   **Paleta de Cores:** A escolha recaiu sobre o Roxo Escuro (`#1c0c3b`) como cor primária, que transmite mistério e sofisticação, contrastando com o Branco para o texto e elementos de destaque. Esta combinação foge aos tradicionais vermelhos e azuis do mercado fitness, conferindo uma identidade única.
*   **Logótipo:** Um capacete espartano estilizado, simbolizando disciplina e resiliência.
*   **Interface:** Foi adotado um design limpo e minimalista, focado na usabilidade. O uso de "Dark Mode" não só é esteticamente agradável como poupa bateria em dispositivos móveis (ecrãs OLED), o que é ideal para uso no ginásio.

### 2. Arquitetura Técnica e Tecnologias
O projeto assenta numa arquitetura robusta e comprovada, o modelo LAMP, adaptado para as necessidades modernas.

*   **Frontend (A Camada Visual):**
    Utilizou-se **HTML5** semântico para a estrutura e **CSS3** avançado para o estilo. Destaca-se o uso de Variáveis CSS (`var(--cor-destaque)`) para facilitar a manutenção do tema e Flexbox/Grid para layouts responsivos. O **JavaScript** (Vanilla JS) foi crucial para a dinamicidade, gerindo chamadas assíncronas (AJAX) para o backend sem recarregar a página, como no caso do Chatbot e das atualizações de progresso.

*   **Backend (A Lógica do Servidor):**
    O **PHP 8** foi a linguagem escolhida pela sua maturidade e facilidade de integração com bases de dados. O código foi estruturado de forma modular. Por exemplo, o ficheiro `ligacao.php` centraliza a conexão à base de dados, enquanto `google_callback.php` gere isoladamente a complexa lógica de autenticação OAuth da Google.

*   **Base de Dados (A Memória do Sistema):**
    O **MySQL/MariaDB** suporta toda a persistência de dados. A estrutura relacional foi desenhada para garantir a integridade dos dados, com uso extensivo de *fkeys* (Chaves Estrangeiras) e *constraints*.

*   **Inteligência Artificial (O Cérebro):**
    A integração com a **Google Gemini API** (`gemini-flash-latest`) é o coração tecnológico do projeto. Diferente de um chatbot simples baseada em regras, este modelo LLM (Large Language Model) permite conversas fluidas e geração criativa de conteúdo.

### 3. Estrutura de Dados Detalhada
A base de dados `berserkfit` é composta por várias tabelas interligadas, desenhadas para suportar escalabilidade:

1.  **Tabela `user`:** O núcleo do sistema. Além dos campos padrão (id, email, password hash), armazena campos de gamificação como `league` (enum: 'Renegado', 'Viking', 'Berserker', etc.) e `day_streak`. A password é sempre armazenada encriptada (`password_hash`).
2.  **Tabela `treino`:** Armazena os metadados de cada plano de treino (Nome, Foco, Data de Criação).
3.  **Tabela `exercicio`:** Relaciona-se com a tabela `treino` (1:N). Cada treino pode ter múltiplos exercícios. Guarda detalhes como Nome, Séries, Repetições e Grupo Muscular.
4.  **Tabelas de Hábitos (`agua`, `peso`, `habito`):** Permitem o rastreamento diário. A tabela `checklist_diario` é particularmente interessante, pois regista o cumprimento diário de hábitos personalizados pelo utilizador.

### 4. Funcionalidades Detalhadas

#### 4.1. Sistema de Autenticação Híbrido e Onboarding
O BerserkFit oferece flexibilidade no acesso. O utilizador pode registar-se manualmente (com validação de email e encriptação forte) ou usar a sua conta Google.
Um desafio técnico interessante foi o fluxo do "Primeiro Acesso Google". Se um utilizador entra com o Google, o sistema cria a conta automaticamente, mas redireciona-o para uma página especial (`escolher_username.php`) para que ele possa escolher um nome de utilizador único, garantindo a consistência da base de dados.

#### 4.2. Dashboard Gamificado
O Dashboard (`dashboard.php`) não é apenas um painel de controlo; é uma ferramenta de motivação.
*   **Streaks:** Um contador de "dias seguidos" incentiva o utilizador a não quebrar a corrente.
*   **Níveis:** O sistema visualiza a Liga atual do utilizador.
*   **Gráficos em Tempo Real:** Barras de progresso dinâmicas mostram o quanto falta para atingir a meta diária de água (ex: 2L/3L) ou calorias, calculadas com base nos inputs do utilizador.

#### 4.3. O Personal Trainer IA (Chatbot)
Esta é a funcionalidade de destaque. O ficheiro `chatbot.js` contém a lógica de comunicação.
*   **Engenharia de Prompt:** Foi desenvolvido um "System Prompt" complexo que instrui o Gemini a agir como um PT profissional, seguro (que avisa sobre limites físicos) e motivador.
*   **Geração Estruturada:** A IA foi instruída a, quando solicitada a criar um treino, gerar não só o texto para o utilizador ler, mas também um bloco de código **JSON** escondido.
*   **Exportação Automática:** O sistema deteta esse JSON, e gera automaticamente um botão "Salvar Treino". Ao clicar, o JavaScript envia esses dados para o PHP, que os insere diretamente nas tabelas `treino` e `exercicio` da base de dados. Isto elimina a necessidade de o utilizador copiar o treino manualmente.

#### 4.4. Gestão de Treinos
A página `treinos.php` lista todos os treinos em cartões interativos. O utilizador pode:
*   Criar treinos do zero.
*   Visualizar detalhes (séries/repetições).
*   **Modo de Execução:** Ao iniciar um treino (`executar_treino.php`), o utilizador entra num modo focado onde pode marcar cada exercício como "Concluído" à medida que o realiza no ginásio.

#### 4.5. Autenticação de Dois Fatores (2FA)
Para elevar o nível de segurança da plataforma, foi implementada a Autenticação de Dois Fatores (2FA). Esta funcionalidade garante que, mesmo que a password de um utilizador seja comprometida, a conta permaneça protegida por um código dinâmico gerado no telemóvel do utilizador. O sistema foi integrado utilizando a biblioteca robusta `robthree/twofactorauth`, permitindo a sincronização via QR Code com aplicações como o Google Authenticator ou Authy.

### 5. Dificuldades Sentidas e Soluções

*   **Identidade e Branding:** A primeira barreira foi a criação de uma marca forte que não fosse apenas "mais uma" no mercado. O desafio foi encontrar um equilíbrio visual entre o nome de inspiração nórdica e o logótipo espartano. A solução foi o desenvolvimento do conceito de "Guerreiro Universal", onde a paleta de cores e os símbolos trabalham em conjunto para comunicar intensidade e disciplina em simultâneo.
*   **Planeamento e UX/UI:** A filtragem de funcionalidades foi complexa. O desafio residiu em estruturar as páginas e a arquitetura da informação de forma lógica, garantindo que o acesso às ferramentas fosse intuitivo para o utilizador.
*   **Integração de IA e APIs:** O estudo sobre APIs foi extenso. A principal dificuldade técnica surgiu na gestão da assincronia da API do Google e na formatação das respostas da IA. A solução passou por aprimorar os prompts do sistema para forçar um esquema JSON rigoroso.
*   **Autenticação e Sincronização de Dados:** Um dos maiores desafios foi a sincronização do Login com o Google. Foi necessário garantir que a resposta da autenticação externa fosse corretamente processada e convertida numa sessão PHP interna estável. O problema foi resolvido através da refatoração do código de callback e testes modulares para assegurar que o utilizador se mantinha logado ao navegar entre páginas.
*   **Segurança e 2FA:** A implementação da Autenticação de Dois Fatores (2FA) exigiu um cuidado extra na gestão de segredos e na sincronização de tempo para a validação de códigos TOTP. Com o apoio técnico do Dinis Dias, foi possível superar as dificuldades de integração da biblioteca externa e garantir um fluxo de ativação através de QR Code que fosse simultaneamente seguro e de fácil utilização.

---

## Conclusão

O projeto BerserkFit atingiu e superou os objetivos propostos. O que começou como uma ideia para resolver uma dificuldade pessoal transformou-se numa plataforma robusta, tecnologicamente complexa e com valor real para o utilizador final. A combinação de tecnologias web tradicionais com a inovação da Inteligência Artificial provou ser uma aposta vencedora, criando uma experiência de utilizador fluida e moderna.

Este projeto permitiu consolidar conhecimentos adquiridos ao longo do curso, nomeadamente em programação estruturada, bases de dados relacionais e design de interfaces, ao mesmo tempo que explorou novas fronteiras como a integração de APIs de LLMs. O BerserkFit está pronto para ser utilizado, constituindo uma base sólida para futuras evoluções, como a criação de uma aplicação móvel nativa.

---

## Bibliografia e Webgrafia

*   **PHP Documentation:** https://www.php.net/docs.php (Consultado regularmente para funções de backend).
*   **MySQL Reference Manual:** https://dev.mysql.com/doc/refman/8.0/en/ (Para otimização de queries e estrutura).
*   **Google AI for Developers:** https://ai.google.dev/ (Documentação da Gemini API).
*   **Figma:** Utilizado para a prototipagem inicial das interfaces.
*   **DrawSQL:** Utilizado para a modelação visual do esquema de base de dados.
*   **W3Schools:** Referência constante para HTML, CSS e JavaScript.
*   **FontAwesome:** Fonte dos ícones vetoriais utilizados na interface.

---

## Anexos

*   **Anexo A:** Diagrama Entidade-Relacionamento (DER) da Base de Dados BerserkFit.
*   **Anexo B:** Capturas de ecrã do Dashboard e do Chatbot em funcionamento.
*   **Anexo C:** Exemplo de um plano de treino gerado pela IA em formato JSON.
*   **Anexo 2:** Implementação Detalhada da Autenticação de Dois Fatores (2FA).

---

## Anexo 2: Implementação Técnica do 2FA

A implementação da Autenticação de Dois Fatores (2FA) foi um passo crucial para garantir a integridade dos dados dos utilizadores do BerserkFit. Este processo de desenvolvimento foi realizado com a ajuda e colaboração fundamental do meu amigo **Dinis Dias**, que contribuiu com o seu conhecimento técnico para a estruturação lógica do sistema de segurança.

### Explicação do Processo:

1.  **Escolha da Tecnologia:** Utilizámos a biblioteca `robthree/twofactorauth` via Composer, por ser uma solução testada e segura para implementar o algoritmo TOTP (Time-based One-Time Password).
2.  **Configuração da Base de Dados:** Adicionámos o campo `tfa_secret` à tabela `user`. Este campo armazena a chave secreta gerada para cada utilizador (em formato Base32), que é cruzada com o código inserido no momento do login.
3.  **Desenvolvimento do Setup:** No ficheiro `setup_2fa.php`, o sistema gera uma chave única e apresenta-a ao utilizador através de um **QR Code**. Ao escanear este código com uma app (como o Google Authenticator), o telemóvel do utilizador fica sincronizado com o servidor.
4.  **Fluxo de Verificação:**
    *   No login tradicional, o sistema verifica se o utilizador tem o 2FA ativo.
    *   Se ativo, o utilizador é redirecionado para `login_2fa.php`, onde deve introduzir o código de 6 dígitos.
    *   Apenas após a validação correta deste código é que a sessão é plenamente iniciada, garantindo que o acesso é feito pelo legítimo proprietário da conta.
5.  **Interface e UX:** A interface foi desenhada para ser intuitiva, fornecendo instruções claras durante o processo de ativação e garantindo um feedback visual imediato em caso de erro ou sucesso na validação.
