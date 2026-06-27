;(() => {
  const tbody = document.getElementById('tableBody')
  const searchInput = document.getElementById('searchInput')
  const pagination = document.querySelector('.pagination')

  let interns = []
  let filteredInterns = [] // resultado da pesquisa ou ordenação
  let currentPage = 1
  const rowsPerPage = 6

  const normalizeText = (text) =>
    text
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .trim()

  // Carrega empresas
  async function loadInterns() {
    try {
      const response = await fetch('/stagely/backend/routes/internshipInfo.php?action=interners')
      if (!response.ok) throw new Error('Erro ao carregar estagiários')

      interns = await response.json()
      filteredInterns = [...interns]
      console.log(filteredInterns)

      renderTablePage(1)
      renderPagination()
    } catch (error) {
      console.error(error)
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--grey-light);">Erro ao carregar estagiários</td></tr>`
      document.querySelector('.search-wrapper h3').textContent = '0 estagiários encontrados'
    }
  }

  // Renderiza a tabela de acordo com a página
  function renderTablePage(page) {
    currentPage = page
    const start = (page - 1) * rowsPerPage
    const end = start + rowsPerPage
    const pageData = filteredInterns.slice(start, end)

    // Contagem (singular/plural)
    const count = filteredInterns.length
    const text = count === 1 ? '1 estagiário encontrado' : `${count} estagiários encontrados`
    document.querySelector('.search-wrapper h3').textContent = text

    tbody.innerHTML = ''

    if (count === 0) {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--grey-light);">Nenhum estagiário encontrado</td></tr>`
      return
    }

    pageData.forEach((intern) => {
      const tr = document.createElement('tr')
      tr.innerHTML = `
        <td><span class="photo">${intern.nome[0]}</span>${intern.nome}</td>
        <td>${intern.curso}</td>
        <td>${intern.area_estagio}</td>
        <td><span class="status ${intern.status === 'Em execução' ? 'progress' : intern.status === 'Concluído' ? 'finished' : 'pending'}">${intern.status}
        <td>
          <div class="actions">
            <button class="open" data-dropdown-toggle="data-${intern.id}">
              <svg><use href="#dots" /></svg>
            </button>

            <div class="options" id="data-${intern.id}">
              <a href="/stagely/frontend/pages/visualizar-estagiario/index.php?id=${intern.id}" class="item">
                <svg><use href="#eye" /></svg>
                <span>Visualizar</span>
              </a>
              <a href="/stagely/frontend/pages/enviar-avaliacao/index.php?id=${intern.id}" class="item">
                <svg><use href="#form" /></svg>
                <span>Avaliar</span>
              </a>
            </div>
          </div>
        </td>
      `

      tbody.appendChild(tr)
    })

    // Atualiza a paginação
    renderPagination()
  }

  function renderPagination() {
    pagination.innerHTML = ''
    const pageCount = Math.ceil(filteredInterns.length / rowsPerPage)
    if (pageCount <= 1) return // não mostra se só tiver 1 página

    const prevBtn = document.createElement('button')
    prevBtn.disabled = currentPage === 1
    prevBtn.innerHTML = `<svg><use href="#arrow-left" /></svg>`
    prevBtn.addEventListener('click', () => renderTablePage(currentPage - 1))
    pagination.appendChild(prevBtn)

    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button')
      btn.textContent = i
      if (i === currentPage) btn.classList.add('active')
      btn.addEventListener('click', () => renderTablePage(i))
      pagination.appendChild(btn)
    }

    const nextBtn = document.createElement('button')
    nextBtn.disabled = currentPage === pageCount
    nextBtn.innerHTML = `<svg><use href="#arrow-right" /></svg>`
    nextBtn.addEventListener('click', () => renderTablePage(currentPage + 1))
    pagination.appendChild(nextBtn)
  }

  // Atualiza tabela após filtro ou ordenação
  function updateFiltered(data) {
    filteredInterns = data
    renderTablePage(1)
  }

  // Filtra tabela ao digitar
  searchInput.addEventListener('input', () => {
    const term = normalizeText(searchInput.value)

    const filtered = interns.filter((intern) => {
      const aluno = normalizeText(intern.nome)
      const curso = normalizeText(intern.curso)
      const area = normalizeText(intern.area_estagio)
      const status = normalizeText(intern.status)

      return aluno.includes(term) || curso.includes(term) || area.includes(term) || status.includes(term)
    })

    updateFiltered(filtered)
  })

  // Ordenação /Filtragem
  document.querySelectorAll('#data-filter .item').forEach((item) => {
    item.addEventListener('click', () => {
      const value = item.dataset.value
      let result = [...interns]

      switch (value) {
        case 'az':
          result.sort((a, b) => a.nome.localeCompare(b.nome))
          break
        case 'za':
          result.sort((a, b) => b.nome.localeCompare(a.nome))
          break
        case 'em-execucao':
          result = result.filter((intern) => intern.status === 'Em execução')
          break
        case 'concluido':
          result = result.filter((intern) => intern.status === 'Concluído')
          break
        case 'pendente':
          result = result.filter((intern) => intern.status === 'Pendente')
          break
        default:
          result = [...interns]
      }

      updateFiltered(result)
    })
  })

  document.addEventListener('click', (e) => {
    const actionBtn = e.target.closest('.actions .open')

    if (!actionBtn) {
      document.querySelectorAll('tbody tr.selected').forEach((tr) => tr.classList.remove('selected'))
      return
    }

    const tr = actionBtn.closest('tr')

    document.querySelectorAll('tbody tr.selected').forEach((row) => {
      if (row !== tr) row.classList.remove('selected')
    })

    tr.classList.toggle('selected')
  })

  loadInterns()
})()
