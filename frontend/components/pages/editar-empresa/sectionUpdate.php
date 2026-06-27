<section class="update">
  <form onsubmit="updateEnterprise(event)" id="form" novalidate>
    <div class="fields first-row">
      <div class="upload">
        <input type="file" id="photoInput" name="photo" accept=".jpg, .jpeg, .png, image/jpeg, image/png" />
        <div class="photo" style="background-image: url('/stagely/frontend/assets/images/placeholder.webp')">
          <img id="photoPreview" alt="" />
          <div class="icon">
            <svg>
              <use href="#camera"></use>
            </svg>
          </div>
        </div>
      </div>
      <div class="field field-name">
        <input type="text" placeholder="Nome da empresa" spellcheck="false" autocomplete="off" id="name" name="name" />
      </div>
    </div>
    <div class="fields">
      <div class="field">
        <label for="email">E-mail</label>
        <input type="email" placeholder="exemplo@mail.com" spellcheck="false" autocomplete="off" id="email"
          name="email" />
      </div>
      <div class="field">
        <label for="password">Senha</label>
        <input type="password" placeholder="senha#123" spellcheck="false" autocomplete="off" id="password"
          name="password" disabled />
        <svg class="icon-eye">
          <use href="#eye" />
        </svg>
      </div>
    </div>
    <div class="fields">
      <div class="field">
        <label for="area_activity">Área de actuação</label>
        <div class="select" data-multiple="true">
          <div class="selected" data-dropdown-toggle="data-area-activity">
            <span class="text">Selecionar</span>
            <svg>
              <use href="#chevron-down"></use>
            </svg>
          </div>
          <div class="options" id="data-area-activity">
            <div class="search">
              <svg>
                <use href="#search"></use>
              </svg>
              <input type="text" placeholder="Buscar..." />
            </div>
            <ul class="list">
            </ul>
          </div>
          <input type="hidden" name="area_activity" id="area_activity">
        </div>
      </div>
      <div class="field">
        <label for="vacancies">Número de vagas</label>
        <input type="number" id="vacancies" name="vacancies" placeholder="Ex: 45" min="1" />
      </div>
    </div>
    <div class="fields">
      <div class="field">
        <label for="hood">Bairro</label>
        <input type="text" placeholder="Ex: Talatona" spellcheck="false" autocomplete="off" id="hood" name="hood" />
      </div>
      <div class="field">
        <label for="street">Rua</label>
        <input type="text" placeholder="Ex: Rua 21 de Janeiro" spellcheck="false" autocomplete="off" id="street"
          name="street" />
      </div>
    </div>
    <div class="buttons">
      <a href="/stagely/frontend/pages/listar-empresas/index.php" class="btn primary">
        <svg>
          <use href="#arrow-left" />
        </svg>
        <p>Voltar</p>
      </a>
      <button type="submit" class="btn">
        <svg>
          <use href="#success" />
        </svg>
        <p>Salvar</p>
        <span></span>
      </button>
    </div>
  </form>
</section>
