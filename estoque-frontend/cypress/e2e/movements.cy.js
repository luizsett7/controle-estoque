describe('Teste de Cadastro de Movimentos', () => {
  
    beforeEach(() => {
      cy.login('luiz@luiz.com', '12345678'); 
      cy.visit('/movement'); 
    });
  
    it('Deve carregar o formulário corretamente', () => {
        cy.contains('button', 'Adicionar Movimento').click();
        cy.get('select#product').should('be.visible').and('not.be.disabled');
    });
  
    it('Deve cadastrar um novo movimento', () => {
      cy.contains('button', 'Adicionar Movimento').click();
      cy.get('select#product').select(0);
      cy.get('select#moviment_type').select('Entrada');      
      cy.get('input[name="quantity"]').should('be.visible').and('not.be.disabled').type('5');
      cy.get('input[name="price"]').should('be.visible').and('not.be.disabled').type('500');
      cy.get('input[name="reason"]').should('be.visible').and('not.be.disabled').type('Razão Teste');
      cy.get('button[type="submit"]').click();
  
      cy.get('table tbody tr').contains('Notebook Gamer Acer Nitro 5').should('be.visible');
    });    
  });
  
  
  