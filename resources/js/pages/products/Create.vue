<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft } from 'lucide-vue-next';

interface Category {
  id: number;
  name: string;
}

interface Atelier {
  id: number;
  name: string;
}

const props = defineProps<{
  atelier: Atelier;
  categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Produtos',
    href: '/products',
  },
  {
    title: 'Novo Produto',
    href: '/products/create',
  },
];

const form = ref({
  category_id: '',
  name: '',
  short_desc: '',
  description: '',
  price: '',
  is_personalizable: false,
  personalization_hint: '',
  is_available: true,
  is_featured: false,
  position: 0,
});

const errors = ref<Record<string, string>>({});

const submit = () => {
  router.post('/products', form.value, {
    onError: (err) => {
      errors.value = err;
    },
    onSuccess: () => {
      form.value = {
        category_id: '',
        name: '',
        short_desc: '',
        description: '',
        price: '',
        is_personalizable: false,
        personalization_hint: '',
        is_available: true,
        is_featured: false,
        position: 0,
      };
      errors.value = {};
    }
  });
};
</script>

<template>
  <Head title="Novo Produto" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <Link href="/products">
            <Button variant="ghost" size="sm">
              <ArrowLeft class="mr-2 h-4 w-4" />
              Voltar
            </Button>
          </Link>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Informações Básicas</CardTitle>
              <CardDescription>Dados principais do produto</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="space-y-2">
                <Label for="category_id">Categoria</Label>
                <select
                  id="category_id"
                  v-model="form.category_id"
                  class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                >
                  <option value="">Selecione uma categoria (opcional)</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id.toString()">
                    {{ category.name }}
                  </option>
                </select>
                <p v-if="errors.category_id" class="text-sm text-destructive">{{ errors.category_id }}</p>
              </div>

              <div class="space-y-2">
                <Label for="name">Nome do Produto *</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Ex: Vaso Decorativo"
                  required
                />
                <p v-if="errors.name" class="text-sm text-destructive">{{ errors.name }}</p>
              </div>

              <div class="space-y-2">
                <Label for="short_desc">Descrição Curta</Label>
                <Input
                  id="short_desc"
                  v-model="form.short_desc"
                  type="text"
                  placeholder="Breve descrição (máx. 180 caracteres)"
                  maxlength="180"
                />
                <p v-if="errors.short_desc" class="text-sm text-destructive">{{ errors.short_desc }}</p>
              </div>

              <div class="space-y-2">
                <Label for="description">Descrição Completa</Label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  placeholder="Descrição detalhada do produto..."
                  rows="4"
                />
                <p v-if="errors.description" class="text-sm text-destructive">{{ errors.description }}</p>
              </div>

              <div class="space-y-2">
                <Label for="price">Preço *</Label>
                <Input
                  id="price"
                  v-model="form.price"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  required
                />
                <p v-if="errors.price" class="text-sm text-destructive">{{ errors.price }}</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Personalização</CardTitle>
              <CardDescription>Configure se o produto aceita personalização</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_personalizable"
                  :checked="form.is_personalizable"
                  @update:checked="(checked: boolean) => form.is_personalizable = checked"
                />
                <Label for="is_personalizable">Este produto pode ser personalizado</Label>
              </div>

              <div v-if="form.is_personalizable" class="space-y-2">
                <Label for="personalization_hint">Dica de Personalização</Label>
                <Input
                  id="personalization_hint"
                  v-model="form.personalization_hint"
                  type="text"
                  placeholder="Ex: Informe o nome para gravação"
                  maxlength="140"
                />
                <p v-if="errors.personalization_hint" class="text-sm text-destructive">{{ errors.personalization_hint }}</p>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Configurações</CardTitle>
              <CardDescription>Disponibilidade e posicionamento</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_available"
                  :checked="form.is_available"
                  @update:checked="(checked: boolean) => form.is_available = checked"
                />
                <Label for="is_available">Produto disponível</Label>
              </div>

              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_featured"
                  :checked="form.is_featured"
                  @update:checked="(checked: boolean) => form.is_featured = checked"
                />
                <Label for="is_featured">Produto em destaque</Label>
              </div>

              <div class="space-y-2">
                <Label for="position">Posição</Label>
                <Input
                  id="position"
                  v-model.number="form.position"
                  type="number"
                  min="0"
                  placeholder="0"
                />
                <p class="text-sm text-muted-foreground">
                  Define a ordem de exibição (menor número aparece primeiro)
                </p>
                <p v-if="errors.position" class="text-sm text-destructive">{{ errors.position }}</p>
              </div>
            </CardContent>
          </Card>

          <div class="flex gap-4">
            <Button type="submit" class="flex-1">
              Criar Produto
            </Button>
            <Link href="/products">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
