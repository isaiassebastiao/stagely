<section class="update">
  <form id="form" onsubmit="updatePassword(event)" novalidate>
    <div class="field">
      <label for="current_password">Senha actual</label>
      <input type="password" placeholder="actual#123" spellcheck="false" autocomplete="new-password"
        id="current_password" name="current_password" />
      <svg class="icon-eye">
        <use href="#eye" />
      </svg>
    </div>
    <div class="field">
      <label for="new_password">Nova senha</label>
      <input type="password" placeholder="nova#123" spellcheck="false" autocomplete="new-password" id="new_password"
        name="new_password" />
      <svg class="icon-eye">
        <use href="#eye" />
      </svg>
    </div>
    <div class="field">
      <label for="confirm_password">Confirme a senha</label>
      <input type="password" placeholder="senha#123" spellcheck="false" autocomplete="new-password"
        id="confirm_password" name="confirm_password" />
      <svg class="icon-eye">
        <use href="#eye" />
      </svg>
    </div>
    <button type="submit" class="btn">
      <p>Salvar</p>
      <span></span>
    </button>
  </form>
</section>
