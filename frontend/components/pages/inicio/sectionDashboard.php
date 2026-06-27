<section class="dashboard">
  <div class="welcome">
    <div class="content">
      <h2>Bem-vindo à Stagely!</h2>
      <p>A solução digital ideal para gerir e acompanhar estágios de forma simples e eficiente</p>
    </div>
    <div class="select">
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
  <div class="statistic">

    <!-- ESTATÍSTICAS -->
    <div class="stats">
      <div class="stat">
        <div class="icon">
          <svg>
            <use href="#calendar" />
          </svg>
        </div>
        <div class="info">
          <span>Total de estágios</span>
          <h4>18</h4>
        </div>
      </div>
      <div class="stat">
        <div class="icon">
          <svg>
            <use href="#clock" />
          </svg>
        </div>
        <div class="info">
          <span>Estágios pendentes</span>
          <h4>5</h4>
        </div>
      </div>
      <div class="stat">
        <div class="icon">
          <svg>
            <use href="#proccess" />
          </svg>
        </div>
        <div class="info">
          <span>Estágios em execução</span>
          <h4>8</h4>
        </div>
      </div>
      <div class="stat">
        <div class="icon">
          <svg>
            <use href="#success" />
          </svg>
        </div>
        <div class="info">
          <span>Estágios concluídos</span>
          <h4>5</h4>
        </div>
      </div>
    </div>

    <!-- GRÁFICOS -->
    <div class="cards">

      <div class="card">
        <div class="card-header">
          <h3>Alunos</h3>
          <!-- Filtro por curso -->
          <div class="select">
            <div class="selected" data-dropdown-toggle="data-course-students">
              <span class="text">Curso</span>
              <svg>
                <use href="#chevron-down"></use>
              </svg>
            </div>
            <div class="options" id="data-course-students">
              <div class="search">
                <svg>
                  <use href="#search"></use>
                </svg>
                <input type="text" placeholder="Buscar..." />
              </div>
              <ul class="list">
                <!-- cursos serão preenchidos dinamicamente -->
              </ul>
            </div>
            <input type="hidden" name="status" id="course-students">
          </div>
        </div>

        <!-- ÁREA DO GRÁFICO -->
        <div class="chart-area">
          <div class="chart-box">
            <canvas id="studentsChart"></canvas>
            <div class="chart-center">
              <h4>120</h4>
              <span>Alunos</span>
            </div>
          </div>

          <!-- LEGENDA -->
          <div class="legend">
            <!-- itens gerados dinamicamente -->
          </div>
        </div>
      </div>

      <!-- ESTÁGIOS -->
      <div class="card">
        <div class="card-header">
          <h3>Estágios</h3>
          <!-- Filtro por curso -->
          <div class="select">
            <div class="selected" data-dropdown-toggle="data-course-internships">
              <span class="text">Curso</span>
              <svg>
                <use href="#chevron-down"></use>
              </svg>
            </div>
            <div class="options" id="data-course-internships">
              <div class="search">
                <svg>
                  <use href="#search"></use>
                </svg>
                <input type="text" placeholder="Buscar..." />
              </div>
              <ul class="list">
                <!-- cursos serão preenchidos dinamicamente -->
              </ul>
            </div>
            <input type="hidden" name="status" id="course-internships">
          </div>
        </div>

        <div class="chart-area">
          <div class="chart-box">
            <canvas id="internshipsChart"></canvas>
            <div class="chart-center">
              <h4>18</h4>
              <span>Estágios</span>
            </div>
          </div>

          <div class="legend">
            <!-- itens gerados dinamicamente -->
          </div>
        </div>
      </div>
    </div>
</section>