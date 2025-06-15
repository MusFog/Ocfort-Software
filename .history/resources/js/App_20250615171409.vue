<template>
  <div class="wrapper">
    <!-- ——— FORMS (side‑by‑side) ——— -->
    <div class="forms-row">
      <!-- USER FORM -->
      <section class="panel">
        <h2>Користувач</h2>
        <form @submit.prevent="saveUser">
          <label>Імʼя <input v-model="userForm.name" required></label>
          <label>Email <input v-model="userForm.email" type="email" required></label>
          <label>Категорії
            <select v-model="userForm.category_ids" multiple size="4">
              <option v-for="c in categories" :value="c.id" :key="c.id">{{ c.title }}</option>
            </select>
          </label>
          <button type="submit">Додати</button>
        </form>
      </section>

      <!-- CATEGORY FORM -->
      <section class="panel">
        <h2>Категорія</h2>
        <form @submit.prevent="saveCategory">
          <label>Назва <input v-model="categoryForm.title" required></label>
          <button type="submit">Додати</button>
        </form>
      </section>
    </div>

    <!-- ——— TABLES (side‑by‑side) ——— -->
    <div class="tables-row">
      <!-- USERS TABLE -->
      <section class="panel">
        <h3>Список користувачів</h3>
        <table>
          <thead><tr><th>Імʼя</th><th>Email</th><th>Категорії</th><th></th></tr></thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.name }}</td>
              <td>{{ u.email }}</td>
              <td>
                <span v-for="c in u.categories ?? []" :key="c.id">{{ c.title }} </span>
              </td>
              <td><button @click="deleteUser(u.id)">×</button></td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- CATEGORIES TABLE -->
      <section class="panel">
        <h3>Список категорій</h3>
        <table>
          <thead><tr><th>Назва</th><th></th></tr></thead>
          <tbody>
            <tr v-for="c in categories" :key="c.id">
              <td>{{ c.title }}</td>
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

/* --- API helper --- */
const api = axios.create({ baseURL: '/api', headers: { Accept: 'application/json' } });

/* --- reactive state --- */
const users       = ref([]);
const categories  = ref([]);
const userForm    = ref({ name: '', email: '', category_ids: [] });
const categoryForm = ref({ title: '' });

/* --- CRUD helpers --- */
async function fetchUsers()      { users.value      = (await api.get('/users')).data; }
async function fetchCategories() { categories.value = (await api.get('/categories')).data; }

async function saveUser() {
  const { data } = await api.post('/users', {
    name:  userForm.value.name,
    email: userForm.value.email,
    category_ids: userForm.value.category_ids,
  });
  users.value.push(data);
  userForm.value = { name: '', email: '', category_ids: [] };
}
async function deleteUser(id) {
  await api.delete(`/users/${id}`);
  users.value = users.value.filter(u => u.id !== id);
}

async function saveCategory() {
  const { data } = await api.post('/categories', categoryForm.value);
  categories.value.push(data);
  categoryForm.value.title = '';
}
async function deleteCategory(id) {
  await api.delete(`/categories/${id}`);
  categories.value = categories.value.filter(c => c.id !== id);
}

onMounted(() => {
  fetchUsers();
  fetchCategories();
  console.log(categories.value)
});
</script>

<style scoped>
.wrapper      { max-width: 960px; margin: 0 auto; font-family: sans-serif; }
.forms-row,
.tables-row    { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px; }
.panel         { border: 1px solid #ccc; border-radius: 4px; padding: 16px; flex: 1 1 300px; }
.panel h2,
.panel h3     { margin-top: 0; }
.panel form    { display: flex; flex-direction: column; gap: 8px; }
.panel table   { width: 100%; border-collapse: collapse; }
.panel th,
.panel td      { border: 1px solid #ddd; padding: 4px; font-size: 14px; }
button         { cursor: pointer; }
</style>
