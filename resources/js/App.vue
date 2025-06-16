<template>
  <div class="app">
    <header class="header">
      <div class="header-content">
        <h1>Система управління</h1>
        <nav class="nav">
          <a href="#" class="nav-link active">Головна</a>
        </nav>
      </div>
    </header>

    <main class="wrapper">
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
        <button @click="errorMessage = ''">×</button>
      </div>

      <div class="forms-row">
        <section class="panel">
          <h2>Користувач</h2>
          <form @submit.prevent="handleUserSubmit" class="vform">
            <label>
              Імʼя
              <input 
                v-model="userFormData.name" 
                required 
                :disabled="isLoading"
                minlength="2"
                maxlength="50"
              />
            </label>
            <label>
              Email
              <input 
                v-model="userFormData.email" 
                type="email" 
                required 
                :disabled="isLoading"
              />
            </label>
            <button type="submit" :disabled="isLoading">
              {{ userFormData.id ? 'Оновити' : 'Додати' }}
            </button>
          </form>
        </section>

        <section class="panel">
          <h2>Категорія</h2>
          <form @submit.prevent="handleCategorySubmit" class="vform">
            <label>
              Назва
              <input 
                v-model="categoryFormData.title" 
                required 
                :disabled="isLoading"
                minlength="2"
                maxlength="50"
              />
            </label>
            <label>
              Користувачі
              <select 
                v-model="categoryFormData.userId" 
                :disabled="isLoading"
              >
                <option value="">Виберіть користувача</option>
                <option 
                  v-for="user in userList" 
                  :value="user.id" 
                  :key="user.id"
                >
                  {{ user.name }}
                </option>
              </select>
            </label>
            <button type="submit" :disabled="isLoading">
              {{ categoryFormData.id ? 'Оновити' : 'Додати' }}
            </button>
          </form>
        </section>
      </div>

      <div class="tables-row">
        <section class="panel">
          <h3>Список користувачів</h3>
          <div v-if="isLoading" class="loading">Завантаження...</div>
          <table v-else>
            <thead>
              <tr>
                <th>Імʼя</th>
                <th>Email</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in userList" :key="user.id">
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <button @click="handleUserEdit(user)" :disabled="isLoading">✎</button>
                  <button @click="confirmDelete('user', user.id)" :disabled="isLoading">×</button>
                </td>
              </tr>
            </tbody>
          </table>
        </section>

        <section class="panel">
          <h3>Список категорій</h3>
          <div v-if="isLoading" class="loading">Завантаження...</div>
          <table v-else>
            <thead>
              <tr>
                <th>Назва</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categoryList" :key="category.id">
                <td>{{ category.title }}</td>
                <td>
                  <button @click="handleCategoryEdit(category)" :disabled="isLoading">✎</button>
                  <button @click="confirmDelete('category', category.id)" :disabled="isLoading">×</button>
                </td>
              </tr>
            </tbody>
          </table>
        </section>
      </div>

      <div v-if="showDeleteConfirm" class="modal">
        <div class="modal-content">
          <h3>Підтвердження видалення</h3>
          <p>Ви впевнені, що хочете видалити цей запис?</p>
          <div class="modal-actions">
            <button @click="handleDelete" :disabled="isLoading">Так</button>
            <button @click="showDeleteConfirm = false" :disabled="isLoading">Ні</button>
          </div>
        </div>
      </div>
    </main>

    <footer class="footer">
      <div class="footer-content">
        <div class="footer-section">
          <h4>Контакти</h4>
          <p>Email: support@example.com</p>
          <p>Телефон: +380 XX XXX XX XX</p>
        </div>
        <div class="footer-section">
          <h4>Інформація</h4>
          <p>© 2024 Всі права захищені</p>
          <p>Версія 1.0.0</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const apiClient = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' }
});

const userList = ref([]);
const categoryList = ref([]);
const isLoading = ref(false);
const errorMessage = ref('');
const showDeleteConfirm = ref(false);
const deleteType = ref('');
const deleteId = ref(null);

const userFormData = ref({
  id: null,
  name: '',
  email: ''
});

const categoryFormData = ref({
  id: null,
  title: '',
  userId: ''
});

async function handleApiRequest(request) {
  try {
    isLoading.value = true;
    errorMessage.value = '';
    return await request();
  } catch (error) {
    const message = error.response?.data?.message || 'Сталася помилка';
    errorMessage.value = message;
    throw error;
  } finally {
    isLoading.value = false;
  }
}

async function fetchUserList() {
  const response = await handleApiRequest(() => apiClient.get('/users'));
  userList.value = response.data.data;
}

async function fetchCategoryList() {
  const response = await handleApiRequest(() => apiClient.get('/categories'));
  categoryList.value = response.data.data;
}

async function handleUserSubmit() {
  try {
    if (userFormData.value.id) {
      await handleApiRequest(() => 
        apiClient.put(`/users/${userFormData.value.id}`, userFormData.value)
      );
    } else {
      const { name, email } = userFormData.value;
      const response = await handleApiRequest(() => 
        apiClient.post('/users', { name, email })
      );
      if (response.data.data) {
        userList.value.push(response.data.data);
      }
    }
    
    resetUserForm();
    await fetchUserList();
  } catch (error) {
    console.error('Error submitting user:', error);
  }
}

function resetUserForm() {
  userFormData.value = {
    id: null,
    name: '',
    email: ''
  };
}

function handleUserEdit(user) {
  userFormData.value = {
    id: user.id,
    name: user.name,
    email: user.email
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function confirmDelete(type, id) {
  deleteType.value = type;
  deleteId.value = id;
  showDeleteConfirm.value = true;
}

async function handleDelete() {
  try {
    if (deleteType.value === 'user') {
      await handleApiRequest(() => apiClient.delete(`/users/${deleteId.value}`));
      userList.value = userList.value.filter(user => user.id !== deleteId.value);
      await fetchCategoryList();
    } else {
      await handleApiRequest(() => apiClient.delete(`/categories/${deleteId.value}`));
      categoryList.value = categoryList.value.filter(category => category.id !== deleteId.value);
    }
    showDeleteConfirm.value = false;
  } catch (error) {
  }
}

async function handleCategorySubmit() {
  try {
    const categoryData = {
      title: categoryFormData.value.title,
      user_id: categoryFormData.value.userId
    };

    if (categoryFormData.value.id) {
      await handleApiRequest(() => 
        apiClient.put(`/categories/${categoryFormData.value.id}`, categoryData)
      );
    } else {
      const response = await handleApiRequest(() => 
        apiClient.post('/categories', categoryData)
      );
      categoryList.value.push(response.data.data);
    }
    
    resetCategoryForm();
    await fetchCategoryList();
  } catch (error) {
  }
}

function resetCategoryForm() {
  categoryFormData.value = {
    id: null,
    title: '',
    userId: ''
  };
}

function handleCategoryEdit(category) {
  categoryFormData.value = {
    id: category.id,
    title: category.title,
    userId: category.users?.[0]?.id || ''
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(async () => {
  await Promise.all([fetchUserList(), fetchCategoryList()]);
});
</script>

<style scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.header {
  background-color: #2d3748;
  color: white;
  padding: 16px 0;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
}

.header h1 {
  margin: 0;
  font-size: 24px;
}

.nav {
  display: flex;
  margin-left: auto;
}

.nav-link {
  color: #e2e8f0;
  text-decoration: none;
  margin-left: 20px;
}

.nav-link.active {
  color: white;
  border-bottom: 2px solid #4299e1;
}

.wrapper {
  flex: 1;
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.forms-row {
  display: flex;
  margin: 0 -10px;
  margin-bottom: 40px;
}

.tables-row {
  display: flex;
  margin: 0 -10px;
  margin-bottom: 40px;
}

.panel {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
  margin: 0 10px;
  width: calc(50% - 20px);
}

.panel h2, .panel h3 {
  margin-top: 0;
  margin-bottom: 20px;
}

.vform {
  margin-bottom: 20px;
}

.vform label {
  display: block;
  margin-bottom: 10px;
}

.vform input,
.vform select {
  width: 100%;
  padding: 8px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  margin-bottom: 15px;
}

.vform select[multiple] {
  height: 120px;
}

button {
  background-color: #4299e1;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
}

button:disabled {
  background-color: #cbd5e0;
}

.panel table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 16px;
}

.panel th,
.panel td {
  padding: 12px;
  border-bottom: 1px solid #e2e8f0;
  text-align: left;
}

.panel th {
  background-color: #f7fafc;
}

.panel td button {
  margin-right: 8px;
}

.panel td button:last-child {
  margin-right: 0;
}

.error-message {
  background-color: #fff5f5;
  color: #c53030;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
  border: 1px solid #feb2b2;
  display: flex;
}

.error-message button {
  background: none;
  color: #c53030;
  border: none;
  font-size: 18px;
  margin-left: auto;
}

.loading {
  text-align: center;
  padding: 32px;
  color: #718096;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
}

.modal-content {
  background-color: white;
  padding: 24px;
  border-radius: 8px;
  width: 320px;
  margin: auto;
}

.modal-content h3 {
  margin-top: 0;
}

.modal-actions {
  margin-top: 24px;
  display: flex;
}

.modal-actions button {
  margin-left: 10px;
}

.modal-actions button:last-child {
  background-color: #e2e8f0;
  color: #4a5568;
}

.footer {
  background-color: #2d3748;
  color: #e2e8f0;
  padding: 40px 0;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
}

.footer-section {
  width: 50%;
  padding: 0 20px;
}

.footer-section h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
}

.footer-section p {
  margin: 8px 0;
  font-size: 14px;
}
</style>
