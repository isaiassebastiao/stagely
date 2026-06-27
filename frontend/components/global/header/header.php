<header>

  <div class="screen">

    <div class="menu-icons">
      <a href="/stagely/frontend/pages/inicio/index.php">
        <img src="/stagely/frontend/assets/images/icon.svg" alt="logo" />
      </a>

      <div class="screen-title">
        <div class="icon">
          <svg>
            <use href="#home" />
          </svg>
        </div>
        <h3>Dashboard</h3>
      </div>
    </div>

    <div class="menu-icon-container">
      <div class="menu-icon">
        <svg>
          <use href="#hamburguer" />
        </svg>
      </div>
    </div>

    <menu id="menu">

      <div class="menu-header">
        <a href="/stagely/frontend/pages/inicio/index.php" class="menu-logo">
          <img src="/stagely/frontend/assets/images/logo.svg" alt="logo" />
        </a>
        <div class="close-menu-icon" id="close_menu_icon">
          <svg>
            <use href="#close"></use>
          </svg>
        </div>
      </div>

      <nav>
      </nav>

    </menu>

  </div>

  <div class="search-and-profile">
    <div class="search">
      <div class="field">
        <div class="icon">
          <svg>
            <use href="#search" />
          </svg>
        </div>
        <input type="text" placeholder="Buscar na aplicação" id="headerSearchInput"
          data-dropdown-toggle="search-shortcuts" />
      </div>

      <div class="options" id="search-shortcuts">
        <h3>Acções rápidas</h3>
        <div id="shortcuts">
          <a href="/stagely/frontend/pages/cadastrar-empresa/index.php" class="item" data-search="cadastrar empresa">
            <svg>
              <use href="#plus"></use>
            </svg>
            <span>Cadastrar empresa</span>
          </a>
          <a href="/stagely/frontend/pages/cadastrar-estagio/index.php" class="item" data-search="cadastrar estágio">
            <svg>
              <use href="#plus"></use>
            </svg>
            <span>Cadastrar estágio</span>
          </a>
        </div>
        <h3>Perfil</h3>
        <a href="/stagely/frontend/pages/editar-perfil/index.php" class="item" data-search="editar perfil">
          <svg>
            <use href="#pencil"></use>
          </svg>
          <span>Editar perfil</span>
        </a>
        <a href="/stagely/frontend/pages/alterar-senha/index.php" class="item" data-search="alterar senha">
          <svg>
            <use href="#lock"></use>
          </svg>
          <span>Alterar senha</span>
        </a>
        <div class="no-results" style="display: none;">Sem resultados</div>
      </div>
    </div>

    <div class="profile">
      <button class="open" data-dropdown-toggle="profile">
        <svg>
          <use href="#chevron-down"></use>
        </svg>
        <h4 id="profileName">
          <?= $_SESSION["name"] ?>
        </h4>
        <div class="photoProfile" style="background-image: url('/stagely/frontend/assets/images/placeholder.webp');">
        </div>
      </button>

      <div class="options" id="profile">
        <h4 id="profileName">
          <?= $_SESSION["name"] ?>
        </h4>
        <a href="/stagely/frontend/pages/editar-perfil/index.php" class="item">
          <svg>
            <use href="#pencil"></use>
          </svg>
          <span>Editar perfil</span>
        </a>
        <a href="/stagely/frontend/pages/alterar-senha/index.php" class="item">
          <svg>
            <use href="#lock"></use>
          </svg>
          <span>Alterar senha</span>
        </a>
        <button class="item" id="themeToggle" data-theme-toggle>
          <svg>
            <use href="#moon"></use>
          </svg>
          <span>Modo escuro</span>
        </button>
        <a href="/stagely/backend/pages/logout.php" class="item logout">
          <svg>
            <use href="#power"></use>
          </svg>
          <span>Terminar sessão</span>
        </a>
      </div>

    </div>
  </div>

</header>
