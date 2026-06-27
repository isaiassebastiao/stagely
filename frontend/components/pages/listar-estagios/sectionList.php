<section class="list">
  <div class="header">
    <div class="search-wrapper">
      <h3></h3>
      <div class="search">
        <div class="field">
          <div class="icon">
            <svg>
              <use href="#search" />
            </svg>
          </div>
          <input type="text" placeholder="Buscar estágio" spellcheck="false" autocomplete="off" id="searchInput" />
        </div>
      </div>
    </div>
    <div class="buttons">
      <div class="filter">
        <button class="btn primary" data-dropdown-toggle="data-filter">
          <svg>
            <use href="#filter"></use>
          </svg>
          <span>Filtrar</span>
        </button>

        <div class="options" id="data-filter">
          <ul class="list">
            <li class="item" data-type="order" data-value="az">Alfabética A–Z</li>
            <li class="item" data-type="order" data-value="za">Alfabética Z–A</li>
            <li class="item" data-type="order" data-value="pendente">Status Pendente</li>
            <li class="item" data-type="order" data-value="em-execucao">Status Em execução</li>
            <li class="item" data-type="order" data-value="concluido">Status Concluído</li>
          </ul>
        </div>
        <input type="hidden" id="order">
      </div>

      <a href="/stagely/frontend/pages/cadastrar-estagio/index.php" class="btn">
        <svg>
          <use href="#plus" />
        </svg>
        <p>Novo estágio</p>
      </a>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Empresa</th>
        <th>Área de estágio</th>
        <th>Período</th>
        <th>Horário</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="tableBody">
    </tbody>
  </table>

  <div class="pagination">
  </div>
</section>
