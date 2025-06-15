<template>
  <div class="wrapper">
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
              @input="clearUserError"
            />
            <span v-if="userError" class="error">{{ userError }}</span>
          </label>
          <label>
            Email
            <input 
              v-model="userFormData.email" 
              type="email" 
              required 
              :disabled="isLoading"
              @input="clearUserError"
            />
          </label>
          <button 
            type="submit" 
            :disabled="isLoading"
            class="submit-button"
          >
            <span v-if="isLoading" class="loader"></span>
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
              @input="clearCategoryError"
            />
            <span v-if="categoryError" class="error">{{ categoryError }}</span>
          </label>
          <label>
            Користувачі
            <select 
              v-model="categoryFormData.userIds" 
              multiple 
              size="4"
              :disabled="isLoading"
            >
              <option 
                v-for="user in userList" 
                :value="user.id" 
                :key="user.id"
              >
                {{ user.name }}
              </option>
            </select>
          </label>
          <button 
            type="submit" 
            :disabled="isLoading"
            class="submit-button"
          >
            <span v-if="isLoading" class="loader"></span>
            {{ categoryFormData.id ? 'Оновити' : 'Додати' }}
          </button>
        </form>
      </section>
    </div>

    <div class="tables-row">
      <section class="panel">
        <h3>Список користувачів</h3>
        <div v-if="isLoading" class="loading-overlay">
          <span class="loader"></span>
        </div>
        <table>
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
                <button 
                  @click="handleUserEdit(user)"
                  :disabled="isLoading"
                  class="icon-button"
                >
                  ✎
                </button>
                <button 
                  @click="confirmDelete('user', user.id)"
                  :disabled="isLoading"
                  class="icon-button"
                >
                  ×
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="panel">
        <h3>Список категорій</h3>
        <div v-if="isLoading" class="loading-overlay">
          <span class="loader"></span>
        </div>
        <table>
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
                <button 
                  @click="handleCategoryEdit(category)"
                  :disabled="isLoading"
                  class="icon-button"
                >
                  ✎
                </button>
                <button 
                  @click="confirmDelete('category', category.id)"
                  :disabled="isLoading"
                  class="icon-button"
                >
                  ×
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </div>

    <!-- Модальне вікно підтвердження видалення -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal">
        <h3>Підтвердження видалення</h3>
        <p>Ви впевнені, що хочете видалити цей елемент?</p>
        <div class="modal-buttons">
          <button @click="handleDelete" :disabled="isLoading">Так</button>
          <button @click="showDeleteModal = false" :disabled="isLoading">Ні</button>
        </div>
      </div>
    </div>

    <!-- Сповіщення -->
    <div v-if="notification" :class="['notification', notification.type]">
      {{ notification.message }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const apiClient = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' }
});

// Стани
const userList = ref([]);
const categoryList = ref([]);
const isLoading = ref(false);
const userError = ref('');
const categoryError = ref('');
const showDeleteModal = ref(false);
const deleteType = ref('');
const deleteId = ref(null);
const notification = ref(null);

const userFormData = ref({
  id: null,
  name: '',
  email: ''
});

const categoryFormData = ref({
  id: null,
  title: '',
  userIds: []
});

// Утиліти
function showNotification(message, type = 'success') {
  notification.value = { message, type };
  setTimeout(() => {
    notification.value = null;
  }, 3000);
}

function clearUserError() {
  userError.value = '';
}

function clearCategoryError() {
  categoryError.value = '';
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// API функції
async function fetchUserList() {
  try {
    isLoading.value = true;
    const response = await apiClient.get('/users');
    userList.value = response.data.data;
  } catch (error) {
    showNotification('Помилка завантаження користувачів', 'error');
    console.error('Error fetching users:', error);
  } finally {
    isLoading.value = false;
  }
}

async function fetchCategoryList() {
  try {
    isLoading.value = true;
    const response = await apiClient.get('/categories');
    categoryList.value = response.data.data;
  } catch (error) {
    showNotification('Помилка завантаження категорій', 'error');
    console.error('Error fetching categories:', error);
  } finally {
    isLoading.value = false;
  }
}

async function handleUserSubmit() {
  try {
    if (!validateEmail(userFormData.value.email)) {
      userError.value = 'Невірний формат email';
      return;
    }

    isLoading.value = true;
    if (userFormData.value.id) {
      await apiClient.put(`/users/${userFormData.value.id}`, userFormData.value);
      showNotification('Користувача оновлено');
    } else {
      const response = await apiClient.post('/users', userFormData.value);
      userList.value.push(response.data.data);
      showNotification('Користувача додано');
    }
    
    userFormData.value = {
      id: null,
      name: '',
      email: ''
    };
    
    await fetchUserList();
  } catch (error) {
    userError.value = error.response?.data?.message || 'Помилка збереження користувача';
    console.error('Error saving user:', error);
  } finally {
    isLoading.value = false;
  }
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
  showDeleteModal.value = true;
}

async function handleDelete() {
  try {
    isLoading.value = true;
    if (deleteType.value === 'user') {
      await handleUserDelete(deleteId.value);
    } else {
      await handleCategoryDelete(deleteId.value);
    }
    showDeleteModal.value = false;
  } catch (error) {
    showNotification('Помилка видалення', 'error');
    console.error('Error deleting:', error);
  } finally {
    isLoading.value = false;
  }
}

async function handleUserDelete(userId) {
  try {
    await apiClient.delete(`/users/${userId}`);
    userList.value = userList.value.filter(user => user.id !== userId);
    showNotification('Користувача видалено');
    await fetchCategoryList();
  } catch (error) {
    showNotification('Помилка видалення користувача', 'error');
    console.error('Error deleting user:', error);
  }
}

async function handleCategorySubmit() {
  try {
    isLoading.value = true;
    let categoryId = categoryFormData.value.id;
    
    if (categoryId) {
      await apiClient.put(`/categories/${categoryId}`, {
        title: categoryFormData.value.title,
        user_id: categoryFormData.value.userIds
      });
      showNotification('Категорію оновлено');
    } else {
      const response = await apiClient.post('/categories', {
        title: categoryFormData.value.title,
        user_id: categoryFormData.value.userIds ?? null
      });
      categoryId = response.data.data.id;
      showNotification('Категорію додано');
    }

    await apiClient.put(`/categories/${categoryId}`, {
      user_id: categoryFormData.value.userIds
    });

    categoryFormData.value = {
      id: null,
      title: '',
      userIds: []
    };
    
    await fetchCategoryList();
  } catch (error) {
    categoryError.value = error.response?.data?.message || 'Помилка збереження категорії';
    console.error('Error saving category:', error);
  } finally {
    isLoading.value = false;
  }
}

function handleCategoryEdit(category) {
  categoryFormData.value = {
    id: category.id,
    title: category.title,
    userIds: (category.users || []).map(user => user.id)
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function handleCategoryDelete(categoryId) {
  try {
    await apiClient.delete(`/categories/${categoryId}`);
    categoryList.value = categoryList.value.filter(category => category.id !== categoryId);
    showNotification('Категорію видалено');
  } catch (error) {
    showNotification('Помилка видалення категорії', 'error');
    console.error('Error deleting category:', error);
  }
}

onMounted(async () => {
  await Promise.all([fetchUserList(), fetchCategoryList()]);
});
</script>

<style scoped>
.wrapper {
  max-width: 960px;
  margin: 0 auto;
  font-family: sans-serif;
  position: relative;
}

.forms-row,
.tables-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-bottom: 40px;
}

.panel {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 16px;
  flex: 1 1 300px;
  position: relative;
}

.vform {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.panel table {
  width: 100%;
  border-collapse: collapse;
}

.panel th,
.panel td {
  border: 1px solid #ddd;
  padding: 4px;
  font-size: 14px;
}

button {
  cursor: pointer;
  padding: 4px 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: #fff;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.submit-button {
  margin-top: 8px;
  padding: 8px;
  background: #4CAF50;
  color: white;
  border: none;
}

.icon-button {
  padding: 2px 6px;
  margin: 0 2px;
}

.error {
  color: #f44336;
  font-size: 12px;
  margin-top: 4px;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
}

.loader {
  width: 20px;
  height: 20px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  display: inline-block;
  margin-right: 8px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: white;
  padding: 20px;
  border-radius: 4px;
  min-width: 300px;
}

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 16px;
}

.notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 12px 24px;
  border-radius: 4px;
  color: white;
  animation: slideIn 0.3s ease-out;
}

.notification.success {
  background: #4CAF50;
}

.notification.error {
  background: #f44336;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>
