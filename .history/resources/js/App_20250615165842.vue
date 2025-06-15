<template>
  <div class="max-w-6xl mx-auto px-4 py-10 space-y-12">
    <!-- ==================== FORMS ROW ==================== -->
    <div class="grid md:grid-cols-2 gap-10">

      <!-- USER FORM -->
      <form @submit.prevent="saveUser" class="bg-white shadow rounded-2xl p-6 space-y-5">
        <h2 class="text-xl font-bold mb-2">Новий користувач / Редагування</h2>

        <div class="space-y-1">
          <label class="block text-sm font-medium">Імʼя</label>
          <input v-model="userForm.name" type="text" class="w-full border rounded-lg p-2" required />
        </div>

        <div class="space-y-1">
          <label class="block text-sm font-medium">Email</label>
          <input v-model="userForm.email" type="email" class="w-full border rounded-lg p-2" required />
        </div>

        <div class="space-y-1">
          <label class="block text-sm font-medium">Категорії</label>
          <select v-model="userForm.category_ids" multiple size="4" class="w-full border rounded-lg p-2 h-32">
            <option v-for="c in categories" :value="c.id" :key="c.id">{{ c.title }}</option>
          </select>
        </div>

        <div class="text-right">
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
            {{ userForm.id ? 'Оновити' : 'Створити' }}
          </button>
        </div>
      </form>

      <!-- CATEGORY FORM -->
      <form @submit.prevent="saveCategory" class="bg-white shadow rounded-2xl p-6 space-y-5">
        <h2 class="text-xl font-bold mb-2">Нова категорія</h2>
        <div class="space-y-1">
          <label class="block text-sm font-medium">Назва</label>
          <input v-model="categoryForm.title" type="text" class="w-full border rounded-lg p-2" required />
        </div>
        <div class="text-right">
          <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow">
            Додати
          </button>
        </div>
      </form>

    </div>

    <!-- ==================== TABLES ROW ==================== -->
    <div class="grid md:grid-cols-2 gap-10">

      <!-- USER TABLE -->
      <div class="bg-white shadow rounded-2xl p-6 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-4">Список користувачів</h3>
        <table class="w-full text-sm text-left whitespace-nowrap">
          <thead class="text-xs uppercase bg-gray-100">
            <tr>
              <th class="px-4 py-3">Імʼя</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Категорії</th>
              <th class="px-4 py-3 w-24 text-center">Дії</th>
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

      <!-- CATEGORY TABLE -->
      <div class="bg-white shadow rounded-2xl p-6 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-4">Список категорій</h3>
        <table class="w-full text-sm text-left">
          <thead class="text-xs uppercase bg-gray-100">
            <tr>
              <th class="px-4 py-3">Назва</th>
              <th class="px-4 py-3 w-24 text-center">Дії</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in categories" :key="c.id" class="border-b hover:bg-gray-50">
              <td class="px-4 py-3">{{ c.title }}</td>
              <td class="px-4 py-3 text-center">
                <button class="text-red-600 hover:text-red-800" @click="deleteCategory(c.id)"><i class="lucide lucide-trash-2"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const api = axios.create({ baseURL: '/api', headers: { Accept: 'application/json' } });

const users       = ref([]);
const categories  = ref([]);
const userForm    = ref({ id: null, name: '', email: '', category_ids: [] });
const categoryForm = ref({ title: '' });

/* ---------- fetch ---------- */
async function fetchUsers()      { users.value      = (await api.get('/users')).data; }
async function fetchCategories() { categories.value = (await api.get('/categories')).data; }

/* ---------- user helpers ---------- */
function resetUserForm() {
  userForm.value = { id: null, name: '', email: '', category_ids: [] };
}
function editUser(u) {
  userForm.value = {
    id: u.id,
    name: u.name,
    email: u.email,
    category_ids: (u.categories || []).map(c => c.id),
  };
}
async function saveUser() {
  const payload = { name: userForm.value.name, email: userForm.value.email };
  let userId = userForm.value.id;
  if (userId) {
    await api.put(`/users/${userId}`, payload);
  } else {
    const { data } = await api.post('/users', payload);
    userId = data.id;
  }
  // sync categories — повна заміна
  await api.put(`/users/${userId}`, { category_ids: userForm.value.category_ids });
  await fetchUsers();
  resetUserForm();
}
async function deleteUser(id) {
  await api.delete(`/users/${id}`);
  users.value = users.value.filter(u => u.id !== id);
}

/* ---------- category helpers ---------- */
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
});
</script>

<style scoped>
/***** Tailwind бере на себе основний стиль. *****/
</style>
