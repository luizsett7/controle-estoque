describe('Teste de Cadastro de Categorias', () => {
  
  beforeEach(() => {
    cy.login('luiz@luiz.com', '12345678'); 
    cy.visit('/category'); 
  });

  it('Deve carregar o formulário corretamente', () => {
    cy.get('input[name="name"]').should('be.visible').and('not.be.disabled');
  });

  it('Deve cadastrar uma nova categoria', () => {
    cy.get('input[name="name"]').should('be.visible').and('not.be.disabled').type('Categoria Teste');
    cy.get('button[type="submit"]').click();

    cy.get('table tbody tr').contains('Categoria Teste').should('be.visible');
  });
});


