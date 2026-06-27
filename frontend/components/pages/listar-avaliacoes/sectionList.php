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
          <input type="text" placeholder="Buscar empresa" spellcheck="false" autocomplete="off" id="searchInput" />
        </div>
      </div>
    </div>
    <div class="buttons">
      <div class="filter select">
        <div class="selected" data-dropdown-toggle="data-filter">
          <svg>
            <use href="#calendar"></use>
          </svg>
          <span class="text">Ano Lectivo</span>
        </div>

        <div class="options" id="data-filter">
          <div class="search">
            <svg>
              <use href="#search"></use>
            </svg>
            <input type="text" placeholder="Buscar..." />
          </div>
          <ul class="list">
            <!-- anos serão preenchidos dinamicamente -->
          </ul>
        </div>
        <input type="hidden" id="order">
      </div>
    </div>
  </div>

  <div class="cards"></div>
  <div class="pagination"></div>
</section>
