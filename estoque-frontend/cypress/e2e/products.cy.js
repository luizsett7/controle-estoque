describe('Teste de Cadastro de Produtos', () => {
  
    beforeEach(() => {
      cy.login('luiz@luiz.com', '12345678'); 
      cy.visit('/product'); 
    });
  
    it('Deve carregar o formulário corretamente', () => {
        cy.contains('button', 'Adicionar Produto').click();
        cy.get('input[name="name"]').should('be.visible').and('not.be.disabled');
    });
  
    it('Deve cadastrar um novo produto', () => {
      cy.contains('button', 'Adicionar Produto').click();
      cy.get('input[name="code"]').should('be.visible').and('not.be.disabled').type('FH190');
      cy.get('input[name="name"]').should('be.visible').and('not.be.disabled').type('Produto Teste');
      cy.get('input[name="description"]').should('be.visible').and('not.be.disabled').type('Descrição Teste');
      cy.get('input[name="expiration_date"]').should('be.visible').and('not.be.disabled').type('2024-11-28');
      cy.get('select#category').select('Eletrônicos');
      cy.get('select#supplier').select('Acer');
      cy.get('input[name="cost_price"]').should('be.visible').and('not.be.disabled').type('550.50');
      cy.get('input[name="sale_price"]').should('be.visible').and('not.be.disabled').type('650.50');
      cy.get('input[name="stock"]').should('be.visible').and('not.be.disabled').type('50');
      cy.get('input[name="min_stock"]').should('be.visible').and('not.be.disabled').type('35');
      cy.get('button[type="submit"]').click();
  
      cy.get('table tbody tr').contains('Produto Teste').should('be.visible');
    });
  });
  
  
  