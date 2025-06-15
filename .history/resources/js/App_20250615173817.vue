<template>
  <div class="wrapper">
    <!-- —— FORMS (side‑by‑side) —— -->
    <div class="forms-row">
      <!-- USER FORM -->
      <section class="panel">
        <h2>Користувач</h2>
        <form @submit.prevent="saveUser" class="vform">
          <label>Імʼя <input v-model="userForm.name" required /></label>
          <label>Email <input v-model="userForm.email" type="email" required /></label>
          <button type="submit">{{ userForm.id ? 'Оновити' : 'Додати' }}</button>
        </form>
      </section>

      <!-- CATEGORY FORM (з вибором користувачів) -->
      <section class="panel">
        <h2>Категорія</h2>
        <form @submit.prevent="saveCategory" class="vform">
          <label>Назва <input v-model="catForm.title" required /></label>
          <label>Користувачі
            <select v-model="catForm.user_ids" multiple size="4">
              <option v-for="u in users" :value="u.id" :key="u.id">{{ u.name }}</option>
            </select>
          </label>
          <button type="submit">{{ catForm.id ? 'Оновити' : 'Додати' }}</button>
        </form>
      </section>
    </div>

    <!-- —— TABLES (side‑by‑side) —— -->
    <div class="tables-row">
      <!-- USERS TABLE -->
      <section class="panel">
        <h3>Список користувачів</h3>
        <table>
          <thead><tr><th>Імʼя</th><th>Email</th><th></th></tr></thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.name }}</td>
              <td>{{ u.email }}</td>
              <td><button @click="deleteUser(u.id)">×</button></td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- CATEGORIES TABLE -->
      <section class="panel">
        <h3>Список категорій</h3>
        <table>
          <thead><tr><th>Назва</th><th>Користувачі</th><th></th></tr></thead>
          <tbody>
            <tr v-for="c in categories" :key="c.id">
              <td>{{ c.title }}</td>
              <td>
                <span v-for="u in c.users ?? []" :key="u.id">{{ u.name }} </span>
              </td>
              <td><button @click="deleteCategory(c.id)">×</button></td>
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

const api = axios.create({ baseURL: '/api', headers: { Accept: 'application/json' } });

/* —— reactive state —— */
const users      = ref([]);
const categories = ref([]);

const userForm = ref({ id: null, name: '', email: '' });
const catForm  = ref({ id: null, title: '', user_ids: [] });

/* —— fetch helpers —— */
async function fetchUsers() {
  const res = await api.get('/users');
  users.value = res.data.data;
}
async function fetchCategories() {
  const res = await api.get('/categories');
  categories.value = res.data.data;
}

/* —— USER CRUD —— */
async function saveUser() {
  if (userForm.value.id) {
    await api.put(`/users/${userForm.value.id}`, userForm.value);
  } else {
    const res = await api.post('/users', userForm.value);
    users.value.push(res.data.data);
  }
  userForm.value = { id: null, name: '', email: '' };
  await fetchUsers();
}
async function deleteUser(id) {
  await api.delete(`/users/${id}`);
  users.value = users.value.filter(u => u.id !== id);
  await fetchCategories(); // оновити зв'язки в таблиці категорій
}

/* —— CATEGORY CRUD + attach users —— */
async function saveCategory() {
  let cid = catForm.value.id;
  if (cid) {
    await api.put(`/categories/${cid}`, { title: catForm.value.title });
  } else {
    const res = await api.post('/categories', { title: catForm.value.title; user_id: catForm.value.user_id });
    cid = res.data.data.id;
  }
  // синхронізуємо користувачів → бекенд-ендпоінт PUT /categories/{id} { user_id }
  await api.put(`/categories/${cid}`, { user_id: catForm.value.user_ids });

  catForm.value = { id: null, title: '', user_ids: [] };
  await fetchCategories();
}
async function deleteCategory(id) {
  await api.delete(`/categories/${id}`);
  categories.value = categories.value.filter(c => c.id !== id);
}

onMounted(async () => {
  await Promise.all([fetchUsers(), fetchCategories()]);
});
</script>

<style scoped>
.wrapper      { max-width: 960px; margin: 0 auto; font-family: sans-serif; }
.forms-row,
.tables-row    { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px; }
.panel         { border: 1px solid #ccc; border-radius: 4px; padding: 16px; flex: 1 1 300px; }
.vform         { display: flex; flex-direction: column; gap: 8px; }
.panel table   { width: 100%; border-collapse: collapse; }
.panel th,
.panel td      { border: 1px solid #ddd; padding: 4px; font-size: 14px; }
button         { cursor: pointer; }
</style>
