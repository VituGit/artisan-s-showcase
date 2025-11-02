<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ref } from 'vue';

interface Atelier {
    id: number;
    slug: string;
    name: string;
    tagline: string | null;
    bio: string | null;
    phone_whatsapp: string | null;
    instagram_handle: string | null;
    email: string | null;
    city: string | null;
    state: string | null;
    is_active: boolean;
}

const props = defineProps<{
    atelier: Atelier;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Editar Ateliê',
        href: '#',
    },
];

const form = ref({
    name: props.atelier.name,
    tagline: props.atelier.tagline || '',
    bio: props.atelier.bio || '',
    phone_whatsapp: props.atelier.phone_whatsapp || '',
    instagram_handle: props.atelier.instagram_handle || '',
    email: props.atelier.email || '',
    city: props.atelier.city || '',
    state: props.atelier.state || '',
    is_active: props.atelier.is_active,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

const submit = () => {
    processing.value = true;
    router.put(`/ateliers/${props.atelier.slug}`, form.value, {
        preserveScroll: true,
        onSuccess: () => {
            errors.value = {};
        },
        onError: (err) => {
            errors.value = err as Record<string, string>;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <Head title="Editar Ateliê" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl space-y-6 p-4 md:p-6">
            <!-- Header -->
            <div class="space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">Editar Ateliê</h1>
                <p class="text-muted-foreground">
                    Atualize as informações do seu ateliê.
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Informações Básicas -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informações Básicas</CardTitle>
                        <CardDescription>
                            Dados principais do seu ateliê que aparecerão na vitrine.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name" required>Nome do Ateliê</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Ex: Ateliê da Nanda"
                                maxlength="150"
                                required
                                :class="{ 'border-destructive': errors.name }"
                            />
                            <p v-if="errors.name" class="text-sm text-destructive">
                                {{ errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="tagline">Slogan</Label>
                            <Input
                                id="tagline"
                                v-model="form.tagline"
                                type="text"
                                placeholder="Ex: Artesanato feito com amor"
                                maxlength="160"
                                :class="{ 'border-destructive': errors.tagline }"
                            />
                            <p v-if="errors.tagline" class="text-sm text-destructive">
                                {{ errors.tagline }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Uma frase curta que representa seu trabalho.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="bio">Sobre o Ateliê</Label>
                            <Textarea
                                id="bio"
                                v-model="form.bio"
                                placeholder="Conte a história do seu ateliê, técnicas utilizadas, inspirações..."
                                rows="5"
                                :class="{ 'border-destructive': errors.bio }"
                            />
                            <p v-if="errors.bio" class="text-sm text-destructive">
                                {{ errors.bio }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Contato -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informações de Contato</CardTitle>
                        <CardDescription>
                            Como os clientes podem entrar em contato com você.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="phone_whatsapp">WhatsApp</Label>
                                <Input
                                    id="phone_whatsapp"
                                    v-model="form.phone_whatsapp"
                                    type="tel"
                                    placeholder="(84) 99999-0000"
                                    maxlength="20"
                                    :class="{ 'border-destructive': errors.phone_whatsapp }"
                                />
                                <p v-if="errors.phone_whatsapp" class="text-sm text-destructive">
                                    {{ errors.phone_whatsapp }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="instagram_handle">Instagram</Label>
                                <div class="flex items-center gap-2">
                                    <span class="text-muted-foreground">@</span>
                                    <Input
                                        id="instagram_handle"
                                        v-model="form.instagram_handle"
                                        type="text"
                                        placeholder="nandaartes"
                                        maxlength="60"
                                        :class="{ 'border-destructive': errors.instagram_handle }"
                                    />
                                </div>
                                <p v-if="errors.instagram_handle" class="text-sm text-destructive">
                                    {{ errors.instagram_handle }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="email">E-mail</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="contato@atelie.com"
                                maxlength="150"
                                :class="{ 'border-destructive': errors.email }"
                            />
                            <p v-if="errors.email" class="text-sm text-destructive">
                                {{ errors.email }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Localização -->
                <Card>
                    <CardHeader>
                        <CardTitle>Localização</CardTitle>
                        <CardDescription>
                            Onde seu ateliê está localizado (opcional).
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2 md:col-span-2">
                                <Label for="city">Cidade</Label>
                                <Input
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    placeholder="Ex: Natal"
                                    maxlength="80"
                                    :class="{ 'border-destructive': errors.city }"
                                />
                                <p v-if="errors.city" class="text-sm text-destructive">
                                    {{ errors.city }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="state">UF</Label>
                                <Input
                                    id="state"
                                    v-model="form.state"
                                    type="text"
                                    placeholder="RN"
                                    maxlength="2"
                                    :class="{ 'border-destructive': errors.state }"
                                />
                                <p v-if="errors.state" class="text-sm text-destructive">
                                    {{ errors.state }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Status -->
                <Card>
                    <CardHeader>
                        <CardTitle>Visibilidade</CardTitle>
                        <CardDescription>
                            Controle se seu ateliê está visível publicamente.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300"
                            />
                            <Label for="is_active" class="cursor-pointer">
                                Ateliê ativo (visível na vitrine)
                            </Label>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submit Button -->
                <div class="flex items-center justify-between gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit('/dashboard')"
                    >
                        Cancelar
                    </Button>
                    <Button
                        type="submit"
                        :disabled="processing"
                        class="min-w-[120px]"
                    >
                        {{ processing ? 'Salvando...' : 'Salvar Alterações' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
