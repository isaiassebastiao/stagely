<section class="register">
  <form onsubmit="updateProfile(event)" id="form" novalidate>
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
        <input type="text" placeholder="Nome do usuário" spellcheck="false" autocomplete="off" id="name" name="name" />
      </div>
    </div>
    <div class="fields">
      <div class="field">
        <label for="email">E-mail</label>
        <input type="email" placeholder="exemplo@mail.com" spellcheck="false" autocomplete="off" id="email"
          name="email" />
      </div>
    </div>
    <div class="buttons">
      <a href="javascript:history.back()" class="btn primary">
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
