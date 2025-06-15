<template>
  <div class="wrapper">
    <div class="forms-row">
      <section class="panel">
        <h2>Користувач</h2>
        <form @submit.prevent="handleUserSubmit" class="vform">
          <label>
            Імʼя
            <input v-model="userFormData.name" required />
          </label>
          <label>
            Email
            <input v-model="userFormData.email" type="email" required />
          </label>
          <button type="submit">
            {{ userFormData.id ? 'Оновити' : 'Додати' }}
          </button>
        </form>
      </section>

      <section class="panel">
        <h2>Категорія</h2>
        <form @submit.prevent="handleCategorySubmit" class="vform">
          <label>
            Назва
            <input v-model="categoryFormData.title" required />
          </label>
          <label>
            Користувачі
            <select v-model="categoryFormData.userIds" multiple size="4">
              <option 
                v-for="user in userList" 
                :value="user.id" 
                :key="user.id"
              >
                {{ user.name }}
              </option>
            </select>
          </label>
          <button type="submit">
            {{ categoryFormData.id ? 'Оновити' : 'Додати' }}
          </button>
        </form>
      </section>
    </div>

    <div class="tables-row">
      <section class="panel">
        <h3>Список користувачів</h3>
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
                <button @click="handleUserEdit(user)">✎</button>
                <button @click="handleUserDelete(user.id)">×</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="panel">
        <h3>Список категорій</h3>
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
                <button @click="handleCategoryEdit(category)">✎</button>
                <button @click="handleCategoryDelete(category.id)">×</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
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

async function fetchUserList() {
  const response = await apiClient.get('/users');
  userList.value = response.data.data;
}

async function fetchCategoryList() {
  const response = await apiClient.get('/categories');
  categoryList.value = response.data.data;
}

async function handleUserSubmit() {
  if (userFormData.value.id) {
    await apiClient.put(`/users/${userFormData.value.id}`, userFormData.value);
  } else {
    const response = await apiClient.post('/users', userFormData.value);
    userList.value.push(response.data.data);
  }
  
  userFormData.value = {
    id: null,
    name: '',
    email: ''
  };
  
  await fetchUserList();
}

function handleUserEdit(user) {
  userFormData.value = {
    id: user.id,
    name: user.name,
    email: user.email
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function handleUserDelete(userId) {
  await apiClient.delete(`/users/${userId}`);
  userList.value = userList.value.filter(user => user.id !== userId);
  await fetchCategoryList();
}

async function handleCategorySubmit() {
  let categoryId = categoryFormData.value.id;
  
  if (categoryId) {
    await apiClient.put(`/categories/${categoryId}`, {
      title: categoryFormData.value.title,
      user_id: categoryFormData.value.userIds
    });
  } else {
    const response = await apiClient.post('/categories', {
      title: categoryFormData.value.title,
      user_id: categoryFormData.value.userIds ?? null
    });
    categoryId = response.data.data.id;
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
  await apiClient.delete(`/categories/${categoryId}`);
  categoryList.value = categoryList.value.filter(category => category.id !== categoryId);
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
</style>
