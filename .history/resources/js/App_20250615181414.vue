<template>
  <div class="wrapper">
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

    <!-- Модальне вікно підтвердження видалення -->
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
  userIds: []
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
      const response = await handleApiRequest(() => 
        apiClient.post('/users', userFormData.value)
      );
      userList.value.push(response.data.data);
    }
    
    resetUserForm();
    await fetchUserList();
  } catch (error) {
    // Помилка вже оброблена в handleApiRequest
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
    // Помилка вже оброблена в handleApiRequest
  }
}

async function handleCategorySubmit() {
  try {
    const categoryData = {
      title: categoryFormData.value.title,
      user_id: categoryFormData.value.userIds
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
    // Помилка вже оброблена в handleApiRequest
  }
}

function resetCategoryForm() {
  categoryFormData.value = {
    id: null,
    title: '',
    userIds: []
  };
}

function handleCategoryEdit(category) {
  categoryFormData.value = {
    id: category.id,
    title: category.title,
    userIds: (category.users || []).map(user => user.id)
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
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
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.error-message {
  background-color: #ffebee;
  color: #c62828;
  padding: 12px;
  border-radius: 4px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.loading {
  text-align: center;
  padding: 20px;
  color: #666;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 4px;
  min-width: 300px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
</style>
