const tbody = document.getElementById('tableBody')
const searchInput = document.getElementById('searchInput')
const pagination = document.querySelector('.pagination')

let enterprises = []
let filteredEnterprises = [] // resultado da pesquisa ou ordenação
let currentPage = 1
const rowsPerPage = 6 // máximo de empresas por página

// Carrega empresas
async function loadEnterprises() {
  try {
    const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=listEnterprises')
    if (!response.ok) throw new Error('Erro ao carregar empresas')

    enterprises = await response.json()
    console.log(enterprises)
    filteredEnterprises = [...enterprises]

    renderTablePage(1)
    renderPagination()
  } catch (error) {
    console.error(error)
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--grey-light);">Erro ao carregar empresas</td></tr>`
    document.querySelector('.search-wrapper h3').textContent = '0 empresas cadastradas'
  }
}

// Renderiza a tabela de acordo com a página
function renderTablePage(page) {
  currentPage = page
  const start = (page - 1) * rowsPerPage
  const end = start + rowsPerPage
  const pageData = filteredEnterprises.slice(start, end)

  // Contagem (singular/plural)
  const count = filteredEnterprises.length
  const text = count === 1 ? '1 empresa cadastrada' : `${count} empresas cadastradas`
  document.querySelector('.search-wrapper h3').textContent = text

  tbody.innerHTML = ''

  if (count === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--grey-light);">Nenhuma empresa encontrada</td></tr>`
    return
  }

  pageData.forEach((enterprise) => {
    const tr = document.createElement('tr')
    tr.innerHTML = `
      ${
        enterprise.imagem_perfil
          ? `<td>
          <div style="display:flex;align-items:center;">
            <img class="photo" src="${enterprise.imagem_perfil}">${enterprise.nome}
          </div>
        </td>`
          : `<td><span class="photo">${enterprise.nome[0]}</span>${enterprise.nome}</td>`
      }
      <td>${enterprise.ramo_atuacao}</td>
      <td>${enterprise.bairro}</td>
      <td>${enterprise.vagas}</td>
      <td><span class="status ${enterprise.status === 'Activo' ? 'active' : 'inactive'}">${enterprise.status}</span></td>
    <td>
      <div class="actions">
        <button class="open" data-dropdown-toggle="data-${enterprise.id}">
          <svg><use href="#dots" /></svg>
        </button>

        <div class="options" id="data-${enterprise.id}">
          <a href="/stagely/frontend/pages/visualizar-empresa/index.php?id=${enterprise.id}" class="item">
            <svg><use href="#eye" /></svg>
            <span>Visualizar</span>
          </a>
          <a href="/stagely/frontend/pages/editar-empresa/index.php?id=${enterprise.id}" class="item">
            <svg><use href="#pencil" /></svg>
            <span>Editar</span>
          </a>
          <button class="item delete" data-id="${enterprise.id}">
            <svg><use href="#trash" /></svg>
            <span>Excluir</span>
          </button>
        </div>
      </div>
    </td>
  `
    const deleteBtn = tr.querySelector('.delete')
    deleteBtn.addEventListener('click', () => {
      const id = parseInt(deleteBtn.dataset.id)
      deleteEnterprise(id)
    })
    tbody.appendChild(tr)
  })
  // Atualiza a paginação
  renderPagination()
}

function renderPagination() {
  pagination.innerHTML = ''
  const pageCount = Math.ceil(filteredEnterprises.length / rowsPerPage)
  if (pageCount <= 1) return // não mostra se só tiver 1 página

  const prevBtn = document.createElement('button')
  prevBtn.disabled = currentPage === 1
  prevBtn.innerHTML = `
    <svg>
      <use href="#arrow-left" />
    </svg>
  `
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
  nextBtn.innerHTML = `
    <svg>
      <use href="#arrow-right" />
    </svg>
  `
  nextBtn.addEventListener('click', () => renderTablePage(currentPage + 1))
  pagination.appendChild(nextBtn)
}

// Atualiza tabela após filtro ou ordenação
function updateFiltered(data) {
  filteredEnterprises = data
  renderTablePage(1)
}

// Filtra tabela ao digitar
searchInput.addEventListener('input', () => {
  const term = searchInput.value.toLowerCase()
  const filtered = enterprises.filter((enterprise) => enterprise.nome.toLowerCase().includes(term) || enterprise.ramo_atuacao.toLowerCase().includes(term) || enterprise.bairro.toLowerCase().includes(term) || enterprise.status.toLowerCase().includes(term))
  updateFiltered(filtered)
})

// Ordenação /Filtragem
document.querySelectorAll('#data-filter .item').forEach((item) => {
  item.addEventListener('click', () => {
    const value = item.dataset.value
    let sorted = [...filteredEnterprises]

    sorted.sort((a, b) => {
      switch (value) {
        case 'az':
          return a.nome.localeCompare(b.nome)

        case 'za':
          return b.nome.localeCompare(a.nome)

        case 'activo':
          return (b.status === 'Activo') - (a.status === 'Activo')

        case 'inactivo':
          return (b.status === 'Inactivo') - (a.status === 'Inactivo')

        default:
          return 0
      }
    })

    updateFiltered(sorted)
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

// Inicializa
loadEnterprises()
