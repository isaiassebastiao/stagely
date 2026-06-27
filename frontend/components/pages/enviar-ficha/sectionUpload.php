<section class="upload">

  <form id="form" onsubmit="uploadEvaluations(event)" id="form" novalidate>

    <div class="icon">
      <svg>
        <use href="#form"></use>
      </svg>
    </div>

    <div class="header">
      <h1>Enviar avaliação</h1>
      <p>
        Submeta a avaliação do estagiário para
        actualização do seu processo de estágio
      </p>
    </div>

    <label class="upload-box" for="fileInput">
      <svg>
        <use href="#cloud"></use>
      </svg>
      <span id="fileName">
        Apenas ficheiros no formato PDF são aceites
      </span>
    </label>

    <input type="file" id="fileInput" name="avaliacao" accept=".pdf">

    <button type="submit" class="btn">
      <p>Enviar</p>
      <span></span>
    </button>

  </form>

</section>
