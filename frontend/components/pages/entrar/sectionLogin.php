<section class="login">
  <img src="/stagely/frontend/assets/images/logo.svg" alt="logo" class="logo" />
  <h1>Inicie sessão para gerir os estágios</h1>
  <form onsubmit="handleLogin(event)" novalidate>
    <div class="field">
      <div class="icon">
        <svg>
          <use href="#mail" />
        </svg>
      </div>
      <label for="email">E-mail</label>
      <input type="email" placeholder="exemplo@mail.com" spellcheck="false" autocomplete="off" id="email"
        name="email" />
    </div>
    <div class="field">
      <div class="icon">
        <svg>
          <use href="#lock" />
        </svg>
      </div>
      <label for="password">Senha</label>
      <input type="password" placeholder="senha#123" spellcheck="false" autocomplete="off" id="password"
        name="password" />
      <div class="icon icon-eye">
        <svg>
          <use href="#eye" />
        </svg>
      </div>
    </div>
    <button type="submit" class="btn">
      <p>Entrar</p>
      <span></span>
    </button>
  </form>
</section>
