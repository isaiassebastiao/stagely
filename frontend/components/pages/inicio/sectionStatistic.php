<section class="statistic">
  <div class="welcome">
    <div class="content">
      <h2>Bem-vindo à Stagely</h2>
      <p>A solução digital ideal para gerir e acompanhar estágios de forma simples e eficiente.</p>
    </div>
    <img src="/stagely/frontend/assets/images/icon.svg" alt="logo" />
  </div>
  <div class="dashboard">

    <!-- ESTATÍSTICAS -->
    <div class="stats">
      <div class="stat">
        <span>Total de Empresas</span>
        <strong>12</strong>
      </div>
      <div class="stat">
        <span>Empresas Ativas</span>
        <strong>9</strong>
      </div>
      <div class="stat">
        <span>Empresas Inativas</span>
        <strong>3</strong>
      </div>
      <div class="stat">
        <span>Total de Estágios</span>
        <strong>18</strong>
      </div>
    </div>

    <!-- GRÁFICOS -->
    <div class="cards">

      <!-- EMPRESAS -->
      <div class="card">
        <h3>Empresas</h3>

        <div class="chart-area">
          <div class="chart-box">
            <canvas id="empresasChart"></canvas>
            <div class="chart-center">
              <strong>12</strong>
              <span>Empresas</span>
            </div>
          </div>

          <div class="legend">
            <div class="legend-item">
              <span class="dot green"></span>9 Ativas
            </div>
            <div class="legend-item">
              <span class="dot red"></span>3 Inativas
            </div>
          </div>
        </div>
      </div>

      <!-- ESTÁGIOS -->
      <div class="card">
        <h3>Estágios</h3>

        <div class="chart-area">
          <div class="chart-box">
            <canvas id="estagiosChart"></canvas>
            <div class="chart-center">
              <strong>18</strong>
              <span>Estágios</span>
            </div>
          </div>

          <div class="legend">
            <div class="legend-item">
              <span class="dot orange"></span>5 Pendentes
            </div>
            <div class="legend-item">
              <span class="dot blue"></span>8 Em andamento
            </div>
            <div class="legend-item">
              <span class="dot green"></span>3 Concluídos
            </div>
            <div class="legend-item">
              <span class="dot red"></span>2 Cancelados
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>

</section>
