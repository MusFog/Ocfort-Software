<!-- src/App.vue -->
<template>
  <div class="wrapper">
    <!-- ─────────────  FORMS  (side-by-side)  ───────────── -->
    <div class="forms-row">
      <!-- USER FORM -->
      <section class="panel">
        <h2>Користувач</h2>

        <form @submit.prevent="saveUser">
          <label>Імʼя
            <input v-model="userForm.name" required>
          </label>

          <label>Email
            <input v-model="userForm.email" type="email" required>
          </label>

          <label>Категорії
            <select v-model="userForm.category_ids" multiple size="4">
              <option v-for="c in categories" :key="c.id" :value="c.id">
                {{ c.title }}
              </option>
            </select>
          </label>

          <button type="submit">
            {{ userForm.id ? 'Оновити' : 'Додати' }}
          </button>
          <button v-if="userForm.id" type="button" @click="resetUserForm">Скасувати</button>
        </form>
      </section>

      <!-- CATEGORY FORM -->
      <section class="panel">
        <h2>Категорія</h2>

        <form @submit.prevent="saveCategory">
          <label>Назва
            <input v-model="categoryForm.title" required>
          </label>

          <button type="submit">Додати</button>
        </form>
      </section>
    </div>

    <!-- ─────────────  TABLES  (side-by-side)  ───────────── -->
    <div class="tables-row">
      <!-- USERS TABLE -->
      <section class="panel">
        <h3>Список користувачів</h3>

        <table>
          <thead>
            <tr><th>Імʼя</th><th>Email</th><th>Категорії</th><th></th></tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.name }}</td>
              <td>{{ u.email }}</td>
              <td>
                <span v-for="c in u.categories ?? []" :key="c.id">
                  {{ c.title }}
                </span>
              </td>
              <td>
                <button @click="editUser(u)">✎</button>
                <button @click="deleteUser(u.id)">×</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- CATEGORIES TABLE -->
      <section class="panel">
        <h3>Список категорій</h3>

        <table>
          <thead>
            <tr><th>Назва</th><th></th></tr>
          </thead>
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
import { ref, onMounted } from 'vue'
import axios from 'axios'

/* ============ API helper ============ */
const api = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' }
})

/* ============ reactive state ============ */
const users       = ref([])
const categories  = ref([])

const userForm = ref({
  id: null,
  name: '',
  email: '',
  category_ids: []
})

const categoryForm = ref({ title: '' })

/* ============ fetch helpers ============ */
const fetchUsers      = async () => users.value      = (await api.get('/users')).data
const fetchCategories = async () => categories.value = (await api.get('/categories')).data

/* ============ user CRUD ============ */
function resetUserForm () {
  userForm.value = { id: null, name: '', email: '', category_ids: [] }
}

function editUser (u) {
  userForm.value = {
    id: u.id,
    name: u.name,
    email: u.email,
    category_ids: (u.categories || []).map(c => c.id)
  }
}

async function saveUser () {
  const payload = {
    name:  userForm.value.name,
    email: userForm.value.email,
    category_ids: userForm.value.category_ids
  }

  if (userForm.value.id) {
    // UPDATE
    await api.put(`/users/${userForm.value.id}`, payload)
  } else {
    // CREATE
    await api.post('/users', payload)
  }

  await fetchUsers()       // оновлюємо таблицю
  resetUserForm()
}

async function deleteUser (id) {
  await api.delete(`/users/${id}`)
  await fetchUsers()
}

/* ============ category CRUD ============ */
async function saveCategory () {
  await api.post('/categories', categoryForm.value)
  await fetchCategories()
  categoryForm.value.title = ''
}

async function deleteCategory (id) {
  await api.delete(`/categories/${id}`)
  await fetchCategories()
}

/* ============ init ============ */
onMounted(async () => {
  await Promise.all([fetchUsers(), fetchCategories()])
})
</script>

<style scoped>
.wrapper          { max-width: 960px; margin: 0 auto; font-family: sans-serif; }
.forms-row,
.tables-row       { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px; }
.panel            { border: 1px solid #ccc; border-radius: 4px; padding: 16px; flex: 1 1 300px; }
.panel h2,
.panel h3         { margin-top: 0; font-size: 18px; }
.panel form       { display: flex; flex-direction: column; gap: 8px; }
.panel table      { width: 100%; border-collapse: collapse; }
.panel th,
.panel td         { border: 1px solid #ddd; padding: 4px; font-size: 14px; }
button            { cursor: pointer; padding: 2px 6px; }
select[multiple]  { width: 100%; }
</style>
