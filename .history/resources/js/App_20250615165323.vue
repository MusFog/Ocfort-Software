<template>
  <div class="max-w-5xl mx-auto px-4 py-8 space-y-12">
    <!-- =============== USER PANEL =============== -->
    <section class="bg-white shadow-lg rounded-2xl p-8 space-y-6">
      <h2 class="text-3xl font-bold mb-4">Користувачі</h2>

      <!-- TABLE -->
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
          <thead class="text-xs uppercase bg-gray-100">
            <tr>
              <th class="px-4 py-3 w-48">Імʼя</th>
              <th class="px-4 py-3 w-64">Email</th>
              <th class="px-4 py-3">Категорії</th>
              <th class="px-4 py-3 w-32 text-center">Дії</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id" class="border-b hover:bg-gray-50">
              <td class="px-4 py-3">{{ u.name }}</td>
              <td class="px-4 py-3">{{ u.email }}</td>
              <td class="px-4 py-3">
                <span v-for="c in u.categories ?? []" :key="c.id" class="inline-block bg-gray-200 rounded-full px-2 py-0.5 mr-1 mb-1 text-xs">{{ c.title }}</span>
              </td>
              <td class="px-4 py-3 text-center space-x-2">
                <button class="text-blue-600 hover:text-blue-800" @click="editUser(u)"><i class="lucide lucide-pencil"></i></button>
                <button class="text-red-600 hover:text-red-800" @click="deleteUser(u.id)"><i class="lucide lucide-trash-2"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FORM -->
      <form @submit.prevent="saveUser" class="grid gap-6 md:grid-cols-3">
        <div class="space-y-1 md:col-span-1">
          <label class="block text-sm font-medium">Імʼя</label>
          <input v-model="userForm.name" type="text" class="w-full border rounded-lg p-2" placeholder="Імʼя" required />
        </div>
        <div class="space-y-1 md:col-span-1">
          <label class="block text-sm font-medium">Email</label>
          <input v-model="userForm.email" type="email" class="w-full border rounded-lg p-2" placeholder="[email protected]" required />
        </div>
        <div class="space-y-1 md:col-span-1">
          <label class="block text-sm font-medium">Категорії</label>
          <select v-model="userForm.category_ids" multiple size="4" class="w-full border rounded-lg p-2 h-32">
            <option v-for="c in categories" :value="c.id" :key="c.id">{{ c.title }}</option>
          </select>
        </div>
        <div class="md:col-span-3 flex justify-end">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
            {{ userForm.id ? 'Оновити' : 'Додати користувача' }}
          </button>
        </div>
      </form>
    </section>

    <!-- =============== CATEGORY PANEL =============== -->
    <section class="bg-white shadow-lg rounded-2xl p-8 space-y-6">
      <h2 class="text-3xl font-bold mb-4">Категорії</h2>

      <!-- LIST -->
      <div class="flex flex-wrap gap-3">
        <span v-for="c in categories" :key="c.id" class="bg-gray-200 px-3 py-1 rounded-full flex items-center text-sm">
          {{ c.title }}
          <button class="ml-2 text-red-600 hover:text-red-800" @click="deleteCategory(c.id)"><i class="lucide lucide-x"></i></button>
        </span>
      </div>

      <!-- FORM -->
      <form class="md:flex gap-4" @submit.prevent="saveCategory">
        <input v-model="categoryForm.title" type="text" placeholder="Нова категорія" class="border rounded-lg p-2 flex-1 mb-3 md:mb-0" required />
        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow">Додати</button>
      </form>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const api = axios.create({ baseURL: '/api', headers: { Accept: 'application/json' } });

/* ==================== STATE ==================== */
const users = ref([]);
const categories = ref([]);
const userForm = ref({ id: null, name: '', email: '', category_ids: [] });
const categoryForm = ref({ title: '' });

/* ==================== FETCH ==================== */
async function fetchUsers() {
  const { data } = await api.get('/users');
  users.value = data;
}
async function fetchCategories() {
  const { data } = await api.get('/categories');
  categories.value = data;
}

/* ==================== USER CRUD ==================== */
function editUser(u) {
  userForm.value = {
    id: u.id,
    name: u.name,
    email: u.email,
    category_ids: (u.categories || []).map(c => c.id),
  };
}

function resetUserForm() {
  userForm.value = { id: null, name: '', email: '', category_ids: [] };
}

async function saveUser() {
  const payload = { name: userForm.value.name, email: userForm.value.email };
  let userId = userForm.value.id;

  if (!userId) {
    const { data } = await api.post('/users', payload);
    userId = data.id;
  } else {
    await api.put(`/users/${userId}`, payload);
  }
  // --- sync categories (toggle)
  await api.put(`/categories/${userForm.value.category_ids[0] ?? ''}`, {
    user_id: userId,
  });
  await fetchUsers();
  resetUserForm();
}

async function deleteUser(id) {
  await api.delete(`/users/${id}`);
  users.value = users.value.filter(u => u.id !== id);
}

/* ==================== CATEGORY CRUD ==================== */
async function saveCategory() {
  const { data } = await api.post('/categories', categoryForm.value);
  categories.value.push(data);
  categoryForm.value.title = '';
}

async function deleteCategory(id) {
  await api.delete(`/categories/${id}`);
  categories.value = categories.value.filter(c => c.id !== id);
}

/* ==================== INIT ==================== */
onMounted(() => {
  fetchUsers();
  fetchCategories();
});
</script>

<style scoped>
/**** додаткові UI-дрібниці, якщо потрібно ****/
</style>
