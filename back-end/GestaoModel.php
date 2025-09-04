<?php

// A classe GestaoModel funciona como um contêiner para os dados da página.
class GestaoModel {
    public $modalTitle = 'Gestão';
    public $contentTitle = 'Guia Prático para Novos Microempreendedores';
    
    // Usamos um array para guardar os parágrafos, facilitando a adição ou remoção de texto.
    public $paragraphs = [];

    public function __construct() {
        $this->paragraphs = [
            "Ser um microempreendedor individual (MEI) é a forma mais simples de formalizar um pequeno negócio no Brasil. Com um CNPJ próprio, você pode emitir notas fiscais, ter acesso a benefícios previdenciários e trabalhar com menos burocracia e baixo custo mensal. Para abrir sua empresa, basta acessar o Portal do Empreendedor, ter em mãos CPF, RG, título de eleitor ou número da última declaração de imposto de renda, endereço residencial e comercial, telefone e e-mail. Em seguida, escolha sua atividade principal (CNAE) e, se necessário, atividades secundárias. Após preencher o cadastro, você receberá o Certificado de Condição de Microempreendedor Individual (CCMEI), que comprova sua formalização.",
            "Como MEI, suas obrigações são simples: pagar mensalmente o DAS-MEI, que varia entre R$ 70,00 e R$ 80,00, entregar a Declaração Anual de Faturamento (DAS-SIMEI) até maio e emitir nota fiscal quando vender ou prestar serviços para empresas. Para manter o negócio saudável, é essencial separar a conta pessoal da conta da empresa, controlar entradas e saídas, guardar parte do lucro para emergências e investimentos e manter pagamentos a fornecedores sempre em dia.",
            "Na organização do dia a dia, planeje suas tarefas usando agenda física ou aplicativos como Google Agenda, Trello ou Notion. Registre todos os clientes e vendas para facilitar contato e fidelização, e, caso trabalhe com produtos, mantenha um estoque organizado para evitar perdas. No marketing, marque presença online com redes sociais e Google Meu Negócio. Publique conteúdos relevantes para seu público, crie promoções e busque parcerias para atrair clientes. Para crescer de forma sustentável, invista em capacitação (o SEBRAE oferece cursos gratuitos), analise regularmente o faturamento e custos, e busque melhorias contínuas no atendimento e na qualidade dos produtos ou serviços.",
            "Por fim, lembre-se: abrir um CNPJ é apenas o primeiro passo. O sucesso de um microempreendedor vem da disciplina, da organização e da constante busca por inovação e clientes. Utilize recursos de apoio como o SEBRAE (www.sebrae.com.br), o próprio Portal do Empreendedor e plataformas de gestão como Conta Azul ou planilhas gratuitas para manter tudo sob controle e crescer com segurança."
        ];
    }

    // Função para obter todos os dados de uma vez
    public function getPageData() {
        return [
            'modalTitle' => $this->modalTitle,
            'contentTitle' => $this->contentTitle,
            'paragraphs' => $this->paragraphs
        ];
    }
}

// Criamos uma instância do modelo para que a View possa usá-la.
$model = new GestaoModel();
$data = $model->getPageData();

?>