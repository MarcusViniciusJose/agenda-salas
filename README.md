
# 📅 Sistema de Agendamento de Salas e Veículos

Um sistema web desenvolvido para gerenciar o agendamento de salas de reunião e veículos corporativos, garantindo **praticidade**, **organização** e **controle** no uso dos recursos internos da empresa.  

O projeto nasceu da necessidade de substituir processos manuais e pouco práticos por uma **solução online, centralizada e acessível**, permitindo que colaboradores façam reservas de forma simples e que a gestão acompanhe tudo em tempo real.

---

## 🚀 Funcionalidades

- **Agendamento de Salas e Veículos** com data, hora e descrição.
- **Controle de Participantes** (apenas o criador pode excluir).
- **Visualização de eventos por dia/mês**.
- **Controle de Acesso** por níveis de usuário:
  - Usuário comum → Pode criar e visualizar seus agendamentos.
  - Administrador → Gerencia todos os agendamentos.
- **Validação de Conflitos** para evitar reservas duplicadas.
- **Integração com feriados** (via *date-holidays* no back-end).
- **Design Responsivo** (uso de Bootstrap).

---

## 🛠 Tecnologias Utilizadas

- **PHP** → Back-end com estrutura **MVC** para melhor organização do código.
- **MySQL** → Banco de dados relacional para armazenar eventos, usuários e recursos.
- **JavaScript** → Interatividade e validações no front-end.
- **Bootstrap** → Layout responsivo e moderno.
- **HTML5 & CSS3** → Estrutura e estilização das páginas.
- **date-holidays** → Biblioteca para gerenciamento automático de feriados.

---

## 🗂 Estrutura do Projeto (MVC)

O sistema segue o padrão **Model-View-Controller (MVC)**:

```
/app
  ├── Config/           # Conexão com o BD
  ├── Controller/       # Controladores do sistema
  ├── Model/            # Modelos e regras de negócio
  ├── View/             # Arquivos de interface com o usuário
/public
  ├── index.php         # Ponto de entrada da aplicação

```

**Por que MVC?**  
O padrão MVC permite **manutenção mais fácil**, **melhor organização do código** e **separação de responsabilidades**, facilitando futuras melhorias e correções.

---

## 📦 Instalação e Configuração

1. **Clone o repositório**  
   ```bash
   git clone https://github.com/seu-usuario/agenda-salas.git
   ```

2. **Configuração do Banco de Dados**  
   - Crie um banco de dados MySQL.
   - Importe o arquivo `database.sql` disponível na pasta `/docs`.

3. **Configuração do Sistema**  
   - Abra `/config/database.php` e ajuste as credenciais do banco:
     ```php
     private $host = "";
     private $db_name = "";
     private $username = "";
     private $password = "";
     ```

4. **Acesse no navegador**  
   ```
   http://localhost/agenda-salas/public
   ```

---

## 📖 Uso Básico

- **Criar Agendamento:** Informe sala, data, hora e participantes.
- **Gerenciar Agendamentos:** O criador pode editar ou excluir.
- **Visualizar Eventos:** Calendário mensal com filtros por recurso.

