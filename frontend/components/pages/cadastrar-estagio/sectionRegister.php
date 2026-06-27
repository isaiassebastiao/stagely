<section class="register">
  <form onsubmit="registerInternship(event)" id="form" novalidate>

    <div class="fields">
      <div class="field">
        <label for="enterprise">Empresa</label>
        <div class="select">
          <div class="selected" data-dropdown-toggle="data-enterprise">
            <span class="text">Selecionar</span>
            <svg>
              <use href="#chevron-down"></use>
            </svg>
          </div>
          <div class="options" id="data-enterprise">
            <div class="search">
              <svg>
                <use href="#search"></use>
              </svg>
              <input type="text" placeholder="Buscar..." />
            </div>
            <ul class="list">
            </ul>
          </div>
          <input type="hidden" name="enterprise" id="enterprise">
        </div>

      </div>

      <div class="field">
        <label for="area_estagio">Área de estágio</label>
        <input type="text" placeholder="Ex: Programação" spellcheck="false" autocomplete="off" id="area_internship"
          name="area_internship" />
      </div>
    </div>

    <div class="fields">
      <div class="field">
        <label for="aluno_id">Alunos</label>
        <div class="select" data-multiple="true">
          <div class="selected" data-dropdown-toggle="data-students">
            <span class="text">Selecionar</span>
            <svg>
              <use href="#chevron-down"></use>
            </svg>
          </div>
          <div class="options" id="data-students">
            <div class="search">
              <svg>
                <use href="#search"></use>
              </svg>
              <input type="text" placeholder="Buscar..." />
            </div>
            <ul class="list">
            </ul>
          </div>
          <input type="hidden" name="students" id="students">
        </div>

      </div>
    </div>

    <div class="fields">
      <div class="field">
        <label for="date_inicio">Data de início</label>
        <input type="date" id="date_start" name="date_start" autocomplete="off" required />
      </div>

      <div class="field">
        <label for="date_end">Data de fim</label>
        <input type="date" id="date_fim" name="date_fim" autocomplete="off" required />
      </div>
    </div>

    <div class="fields days">
      <div class="field">
        <label class="day">
          <input type="checkbox" name="days[]" value="1">
          <span class="check">
            <svg>
              <use href="#check" />
            </svg>
          </span>
          Segunda
        </label>
      </div>

      <div class="field">
        <label class="day">
          <input type="checkbox" name="days[]" value="2">
          <span class="check">
            <svg>
              <use href="#check" />
            </svg>
          </span>
          Terça
        </label>
      </div>

      <div class="field">
        <label class="day">
          <input type="checkbox" name="days[]" value="3">
          <span class="check">
            <svg>
              <use href="#check" />
            </svg>
          </span>
          Quarta
        </label>
      </div>

      <div class="field">
        <label class="day">
          <input type="checkbox" name="days[]" value="4">
          <span class="check">
            <svg>
              <use href="#check" />
            </svg>
          </span>
          Quinta
        </label>
      </div>

      <div class="field">
        <label class="day">
          <input type="checkbox" name="days[]" value="5">
          <span class="check">
            <svg>
              <use href="#check" />
            </svg>
          </span>
          Sexta
        </label>
      </div>

      <div class="field">
        <label class="day">
          <input type="checkbox" name="days[]" value="6">
          <span class="check">
            <svg>
              <use href="#check" />
            </svg>
          </span>
          Sábado
        </label>
      </div>
    </div>

    <div class="fields">
      <div class="field">
        <label for="time_entry">Hora de entrada</label>
        <input type="time" id="time_entry" name="time_entry" placeholder="Ex: 12:00" autocomplete="off" required>
      </div>

      <div class="field">
        <label for="time_out">Hora de saída</label>
        <input type="time" id="time_out" name="time_out" placeholder="Ex: 14:00" autocomplete="off" required>
      </div>
    </div>

    <div class="buttons">
      <a href="/stagely/frontend/pages/listar-estagios/index.php" class="btn primary">
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
