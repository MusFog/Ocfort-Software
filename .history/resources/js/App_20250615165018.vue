<template>
  <div class="container mx-auto px-4 py-6 space-y-10">
    <!-- ================= USER PANEL ================= -->
    <section class="bg-white shadow rounded-2xl p-6 space-y-6">
      <h2 class="text-2xl font-bold mb-2">Користувачі</h2>

      <!-- User list -->
      <div>
        <table class="w-full text-sm text-left">
          <thead class="text-xs uppercase bg-gray-50">
            <tr>
              <th class="px-4 py-2">Імʼя</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Категорії</th>
              <th class="px-4 py-2 w-32">Дії</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id" class="border-b">
              <td class="px-4 py-2">{{ u.name }}</td>
              <td class="px-4 py-2">{{ u.email }}</td>
              <td class="px-4 py-2">
                <span v-for="c in u.categories ?? []" :key="c.id" class="inline-block bg-gray-200 rounded px-2 py-0.5 mr-1 mb-1 text-xs">{{ c.title }}</span>
              </td>
              <td class="px-4 py-2 space-x-2">
                <button class="text-blue-600" @click="editUser(u)"><i class="lucide lucide-pencil"></i></button>
                <button class="text-red-600" @click="deleteUser(u.id)"><i class="lucide lucide-trash-2"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- User form -->
      <form @submit.prevent="saveUser" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
          <label class="block text-sm font-medium mb-1">Імʼя</label>
          <input v-model="userForm.name" type="text" class="w-full border rounded p-2" required />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="userForm.email" type="email" class="w-full border rounded p-2" required />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Категорії</label>
          <select v-model="userForm.category_ids" multiple class="w-full border rounded p-2 h-32">
            <option v-for="c in categories" :value="c.id" :key="c.id">{{ c.title }}</option>
          </select>
        </div>
        <div>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded w-full md:w-auto">{{ userForm.id ? 'Оновити' : 'Створити' }}</button>
        </div>
      </form>
    </section>

    <!-- ================= CATEGORY PANEL ================= -->
    <section class="bg-white shadow rounded-2xl p-6 space-y-6">
      <h2 class="text-2xl font-bold mb-2">Категорії</h2>

      <div class="flex flex-wrap gap-2 mb-4">
        <span v-for="c in categories" :key="c.id" class="bg-gray-200 px-3 py-1 rounded-full flex items-center">
          {{ c.title }}
          <button class="ml-2 text-red-600" @click="deleteCategory(c.id)"><i class="lucide lucide-x"></i></button>
        </span>
      </div>

      <form @submit.prevent="saveCategory" class="flex gap-3">
        <input v-model="categoryForm.title" type="text" placeholder="Назва" class="border rounded p-2 flex-1" required />
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Додати</button>
      </form>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// ---------- state
const users = ref([]);
const categories = ref([]);

const userForm = ref({ id: null, name: '', email: '', category_ids: [] });
const categoryForm = ref({ title: '' });

// ---------- API helpers
const api = axios.create({ baseURL: '/api' });

async function fetchUsers() {
  const { data } = await api.get('/users');
  users.value = data;
}
async function fetchCategories() {
  const { data } = await api.get('/categories');
  categories.value = data;
}

// ---------- CRUD Users
function editUser(u) {
  userForm.value = {
    id: u.id,
    name: u.name,
    email: u.email,
    category_ids: (u.categories || []).map(c => c.id),
  };
}

async function saveUser() {
  const payload = {
    name: userForm.value.name,
    email: userForm.value.email,
  };
  if (userForm.value.id) {
    // UPDATE
    await api.put(`/users/${userForm.value.id}`, payload);
  } else {
    // CREATE
    const { data } = await api.post('/users', payload);
    userForm.value.id = data.id;
  }
  // sync categories
  await Promise.all(userForm.value.category_ids.map(id =>
    api.put(`/categories/${id}`, { user_id: userForm.value.id })
  ));

  await fetchUsers();
  resetUserForm();
}

async function deleteUser(id) {
  await api.delete(`/users/${id}`);
  users.value = users.value.filter(u => u.id !== id);
}

function resetUserForm() {
  userForm.value = { id: null, name: '', email: '', category_ids: [] };
}

// ---------- CRUD Categories
async function saveCategory() {
  const { data } = await api.post('/categories', categoryForm.value);
  categories.value.push(data);
  categoryForm.value.title = '';
}

async function deleteCategory(id) {
  await api.delete(`/categories/${id}`);
  categories.value = categories.value.filter(c => c.id !== id);
}

// ---------- load initial data
onMounted(() => {
  fetchUsers();
  fetchCategories();
});
</script>

<style scoped>
/***** Tailwind classes already handle styling *****/
</style>
