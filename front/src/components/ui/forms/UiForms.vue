<template>
  <div class="ui-forms-showcase">
    <h2>Formulaires</h2>
    
    <div class="form-section">
      <h3>Champs de texte</h3>
      <div class="form-row">
        <div class="form-group">
          <label for="text-input">Champ de texte</label>
          <input type="text" id="text-input" placeholder="Saisissez votre texte">
        </div>
        
        <div class="form-group">
          <label for="disabled-input">Champ désactivé</label>
          <input type="text" id="disabled-input" placeholder="Ce champ est désactivé" disabled>
        </div>
      </div>
    </div>
    
    <div class="form-section">
      <h3>Sélecteurs</h3>
      <div class="form-row">
        <div class="form-group">
          <label for="select-input">Menu déroulant</label>
          <select id="select-input">
            <option value="" disabled selected>Sélectionnez une option</option>
            <option value="option1">Option 1</option>
            <option value="option2">Option 2</option>
            <option value="option3">Option 3</option>
          </select>
        </div>
        
        <div class="form-group">
          <fieldset>
            <legend>Cases à cocher</legend>
            <div class="checkbox-group">
              <div class="checkbox">
                <input type="checkbox" id="checkbox1" name="checkbox1">
                <label for="checkbox1">Option 1</label>
              </div>
              <div class="checkbox">
                <input type="checkbox" id="checkbox2" name="checkbox2">
                <label for="checkbox2">Option 2</label>
              </div>
            </div>
          </fieldset>
        </div>
        
        <div class="form-group">
          <fieldset>
            <legend>Boutons radio</legend>
            <div class="radio-group">
              <div class="radio">
                <input type="radio" id="radio1" name="radioGroup" value="radio1">
                <label for="radio1">Option 1</label>
              </div>
              <div class="radio">
                <input type="radio" id="radio2" name="radioGroup" value="radio2">
                <label for="radio2">Option 2</label>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
    
    <div class="form-section">
      <h3>Exemples de formulaires</h3>
      <form class="example-form">
        <div class="form-group">
          <label for="name">Nom</label>
          <input type="text" id="name" placeholder="Votre nom">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" placeholder="votre.email@exemple.com">
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" rows="4" placeholder="Votre message"></textarea>
        </div>
        <div class="form-buttons">
          <button type="reset" class="secondary">Réinitialiser</button>
          <button type="submit" class="primary">Envoyer</button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.ui-forms-showcase {
  padding: 1rem;
}

.form-section {
  margin-bottom: 2rem;
}

.form-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin: 1rem 0;
}

.form-group {
  margin-bottom: 1rem;
  min-width: 250px;
}

label, legend {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

input[type="text"],
input[type="email"],
input[type="password"],
select,
textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s ease;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
select:focus,
textarea:focus {
  border-color: #3498db;
  outline: none;
}

input[type="text"]:disabled,
input[type="email"]:disabled,
input[type="password"]:disabled,
select:disabled,
textarea:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.checkbox-group,
.radio-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.checkbox,
.radio {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.checkbox label,
.radio label {
  margin-bottom: 0;
  font-weight: normal;
}

fieldset {
  border: 1px solid #ddd;
  padding: 1rem;
  border-radius: 4px;
}

.example-form {
  max-width: 500px;
  padding: 1rem;
  border: 1px solid #eaeaea;
  border-radius: 4px;
}

.form-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

button {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: 1px solid #ccc;
  background-color: #f5f5f5;
  cursor: pointer;
  font-size: 1rem;
}

button.primary {
  background-color: #3498db;
  color: white;
  border-color: #2980b9;
}

button.secondary {
  background-color: #f5f5f5;
  color: #333;
  border-color: #ccc;
}
</style>
