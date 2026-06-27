;(() => {
  const cardsContainer = document.querySelector('.cards')
  const searchInput = document.getElementById('searchInput')
  const pagination = document.querySelector('.pagination')
  const yearsList = document.querySelector('#data-filter .list')
  const headerCount = document.querySelector('.search-wrapper h3')
  const course = new URLSearchParams(window.location.search)
  const selectedCourse = course.get('curso')

  let evaluations = []
  let filteredEvaluations = []
  let currentPage = 1
  const rowsPerPage = 5

  const normalizeText = (text) =>
    text
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .trim()

  async function loadEvaluations() {
    try {
      const getRole = await fetch("/stagely/backend/routes/getRole.php")
      const role = await getRole.json()
      if (role.role === "Escola")
        response = await fetch(`/stagely/backend/routes/adminRoutes.php?action=getEvaluations&curso=${selectedCourse}`)
      else
        response = await fetch(`/stagely/backend/routes/enterpriseRoutes.php?action=getEvaluations`)

      if (!response.ok) throw new Error('Erro ao carregar avaliações')

      evaluations = await response.json()
      evaluations = evaluations.data
      console.log(evaluations)
      filteredEvaluations = [...evaluations]

      renderPage(1)
    } catch (error) {
      console.error(error)
      cardsContainer.innerHTML = `<p style="text-align:center;color:var(--grey-light);font-family:var(--light);font-size:var(--f2)">Erro ao carregar avaliações</p>`
    }
  }

  function renderPage(page) {
    currentPage = page
    const start = (page - 1) * rowsPerPage
    const end = start + rowsPerPage
    const pageData = filteredEvaluations.slice(start, end)

    const count = filteredEvaluations.length
    headerCount.textContent = count === 1 ? '1 avaliação encontrada' : `${count} avaliações encontradas`

    cardsContainer.innerHTML = ''

    if (pageData.length === 0) {
      cardsContainer.innerHTML = `<p style="text-align:center;color:var(--grey-light);font-family:var(--light);font-size:var(--f2)">Nenhuma avaliação encontrada</p>`
      pagination.innerHTML = ''
      return
    }

    pageData.forEach((item) => {
      const card = document.createElement('div')
      card.className = 'card'
      card.innerHTML = `
        <div class="info">
          <h4>${item.nome}</h4>
        </div>
        <div class="actions">
          <a href="${item.caminho_arquivo}" class="btn" target="_blank">
            <svg><use href="#eye" /></svg>
            <p>Ver avaliação</p>
          </a>
        </div>
      `
      cardsContainer.appendChild(card)
    })

    renderPagination()
  }

  function renderPagination() {
    pagination.innerHTML = ''
    const pageCount = Math.ceil(filteredEvaluations.length / rowsPerPage)
    if (pageCount <= 1) return

    const prevBtn = document.createElement('button')
    prevBtn.disabled = currentPage === 1
    prevBtn.innerHTML = `<svg><use href="#arrow-left" /></svg>`
    prevBtn.addEventListener('click', () => renderPage(currentPage - 1))
    pagination.appendChild(prevBtn)

    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button')
      btn.textContent = i
      if (i === currentPage) btn.classList.add('active')
      btn.addEventListener('click', () => renderPage(i))
      pagination.appendChild(btn)
    }

    const nextBtn = document.createElement('button')
    nextBtn.disabled = currentPage === pageCount
    nextBtn.innerHTML = `<svg><use href="#arrow-right" /></svg>`
    nextBtn.addEventListener('click', () => renderPage(currentPage + 1))
    pagination.appendChild(nextBtn)
  }

  searchInput.addEventListener('input', () => {
    const term = normalizeText(searchInput.value)

    filteredEvaluations = evaluations.filter((e) => {
      const nome = normalizeText(e.nome || '')
      return nome.includes(term)
    })

    renderPage(1)
  })

  const getEvaluationYear = (evaluation) => {
    return (
      evaluation.year ||
      evaluation.ano ||
      evaluation.ano_letivo ||
      evaluation['ano lectivo'] ||
      evaluation['ano_lectivo'] ||
      ''
    )
      .toString()
      .trim()
  }

  yearsList.addEventListener('click', (e) => {
    const item = e.target.closest('.item')
    if (!item) return

    document.querySelectorAll('#data-filter .item').forEach((el) => el.classList.remove('active'))
    item.classList.add('active')

    const year = item.dataset.year?.toString().trim()
    filteredEvaluations = year === 'todos'
      ? [...evaluations]
      : evaluations.filter((e) => getEvaluationYear(e) === year)
    renderPage(1)
  })

  loadEvaluations()
})()
