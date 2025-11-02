<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft } from 'lucide-vue-next';

interface Atelier {
  id: number;
  name: string;
  slug: string;
}

const props = defineProps<{
  atelier: Atelier;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Categorias',
    href: '/categories',
  },
  {
    title: 'Nova Categoria',
    href: '/categories/create',
  },
];

const form = ref({
  name: '',
  position: 0,
  is_active: true,
});

const errors = ref<Record<string, string>>({});

const submit = () => {
  router.post('/categories', form.value, {
    onError: (err) => {
      errors.value = err;
    },
    onSuccess: () => {
      form.value = {
        name: '',
        position: 0,
        is_active: true,
      };
      errors.value = {};
    }
  });
};
</script>

<template>
  <Head title="Nova Categoria" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <Link href="/categories">
            <Button variant="ghost" size="sm">
              <ArrowLeft class="mr-2 h-4 w-4" />
              Voltar
            </Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Nova Categoria</CardTitle>
            <CardDescription>Crie uma nova categoria para organizar seus produtos</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="space-y-2">
                <Label for="name">Nome da Categoria *</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Ex: Artesanato em Madeira"
                  required
                />
                <p v-if="errors.name" class="text-sm text-destructive">{{ errors.name }}</p>
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

              <div class="flex items-center space-x-2">
                <Checkbox
                  id="is_active"
                  :checked="form.is_active"
                  @update:checked="(checked: boolean) => form.is_active = checked"
                />
                <Label for="is_active" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                  Categoria ativa
                </Label>
              </div>

              <div class="flex gap-4">
                <Button type="submit" class="flex-1">
                  Criar Categoria
                </Button>
                <Link href="/categories">
                  <Button type="button" variant="outline">
                    Cancelar
                  </Button>
                </Link>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
