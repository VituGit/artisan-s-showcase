<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Edit, Plus, Trash2, Package } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

interface Category {
  id: number;
  name: string;
  slug: string;
  position: number;
  is_active: boolean;
  products_count: number;
  created_at: string;
}

interface Atelier {
  id: number;
  name: string;
  slug: string;
}

const props = defineProps<{
  categories: Category[];
  atelier: Atelier;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Categorias',
    href: '/categories',
  },
];

const deleteCategory = (slug: string) => {
  if (confirm('Tem certeza que deseja excluir esta categoria?')) {
    router.delete(`/categories/${slug}`);
  }
};
</script>

<template>
  <Head title="Categorias" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-3xl font-bold tracking-tight">Categorias</h2>
            <p class="text-muted-foreground">Gerencie as categorias dos seus produtos</p>
          </div>
          <Link href="/categories/create">
            <Button>
              <Plus class="mr-2 h-4 w-4" />
              Nova Categoria
            </Button>
          </Link>
        </div>

        <div v-if="categories.length === 0" class="text-center py-12">
          <Package class="mx-auto h-12 w-12 text-muted-foreground" />
          <h3 class="mt-2 text-sm font-semibold">Nenhuma categoria</h3>
          <p class="mt-1 text-sm text-muted-foreground">Comece criando sua primeira categoria.</p>
          <div class="mt-6">
            <Link href="/categories/create">
              <Button>
                <Plus class="mr-2 h-4 w-4" />
                Nova Categoria
              </Button>
            </Link>
          </div>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <Card v-for="category in categories" :key="category.id">
            <CardHeader>
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <CardTitle>{{ category.name }}</CardTitle>
                  <CardDescription class="mt-1">
                    {{ category.products_count }} produto{{ category.products_count !== 1 ? 's' : '' }}
                  </CardDescription>
                </div>
                <Badge :variant="category.is_active ? 'default' : 'secondary'">
                  {{ category.is_active ? 'Ativa' : 'Inativa' }}
                </Badge>
              </div>
            </CardHeader>
            <CardContent>
              <div class="flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                  Posição: {{ category.position }}
                </div>
                <div class="flex gap-2">
                  <Link :href="`/categories/${category.slug}/edit`">
                    <Button variant="outline" size="sm">
                      <Edit class="h-4 w-4" />
                    </Button>
                  </Link>
                  <Button
                    variant="outline"
                    size="sm"
                    @click="deleteCategory(category.slug)"
                  >
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
