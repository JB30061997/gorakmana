<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2, Users, Wrench, X } from '@lucide/vue';
import { computed, reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { clients, services } from '@/routes/catalog';

type RecordValue = string | number | boolean | null;
type CatalogRecord = Record<string, RecordValue> & { id: number };

const props = defineProps<{ section: 'clients' | 'services'; records: CatalogRecord[] }>();
const search = ref('');
const showForm = ref(false);
const editingId = ref<number | null>(null);
const submitting = ref(false);
const errors = ref<Record<string, string[]>>({});

const clientDefaults = { type: 'company', code: '', raison_sociale: '', ice: '', identifiant_fiscal: '', rc: '', taxe_professionnelle: '', contact_name: '', email: '', telephone: '', website: '', adresse: '', ville: '', pays: 'Maroc', status: 'active', notes: '' };
const serviceDefaults = { code: '', name: '', description: '', billing_type: 'one_time', default_price_ht: 0, default_tva_rate: 20, currency: 'MAD', is_active: true };
const form = reactive<Record<string, any>>({ ...(props.section === 'clients' ? clientDefaults : serviceDefaults) });

const config = computed(() => props.section === 'clients'
    ? { title: 'Clients', subtitle: 'Votre portefeuille clients centralisé.', icon: Users, endpoint: '/admin/clients', route: clients() }
    : { title: 'Services', subtitle: 'Prestations et tarifs utilisés dans vos devis.', icon: Wrench, endpoint: '/admin/services', route: services() });

const filteredRecords = computed(() => {
    const term = search.value.toLowerCase().trim();
    if (!term) return props.records;
    return props.records.filter((record) => Object.values(record).some((value) => String(value ?? '').toLowerCase().includes(term)));
});

function csrfToken(): string {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

function openCreate(): void {
    editingId.value = null;
    Object.keys(form).forEach((key) => delete form[key]);
    Object.assign(form, props.section === 'clients' ? clientDefaults : serviceDefaults);
    errors.value = {};
    showForm.value = true;
}

function openEdit(record: CatalogRecord): void {
    editingId.value = record.id;
    Object.keys(form).forEach((key) => delete form[key]);
    const defaults = props.section === 'clients' ? clientDefaults : serviceDefaults;
    Object.assign(form, defaults, Object.fromEntries(Object.keys(defaults).map((key) => [key, record[key] ?? defaults[key as keyof typeof defaults]])));
    errors.value = {};
    showForm.value = true;
}

async function save(): Promise<void> {
    submitting.value = true;
    errors.value = {};
    try {
        const response = await fetch(editingId.value ? `${config.value.endpoint}/${editingId.value}` : config.value.endpoint, {
            method: editingId.value ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify(form),
        });
        const data = await response.json();
        if (!response.ok) {
            errors.value = data.errors ?? { form: [data.message ?? 'Une erreur est survenue.'] };
            return;
        }
        toast.success(data.message);
        showForm.value = false;
        router.reload();
    } catch {
        toast.error('Impossible de contacter le serveur.');
    } finally {
        submitting.value = false;
    }
}

async function remove(record: CatalogRecord): Promise<void> {
    if (!window.confirm('Confirmer la suppression ?')) return;
    const response = await fetch(`${config.value.endpoint}/${record.id}`, { method: 'DELETE', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() } });
    const data = await response.json();
    if (!response.ok) {
        toast.error(data.message ?? 'Suppression impossible.');
        return;
    }
    toast.success(data.message);
    router.reload();
}

defineOptions({ layout: { breadcrumbs: [] } });
</script>

<template>
    <div class="min-h-full bg-[#f7f7f9] p-4 md:p-7 dark:bg-[#09090b]">
        <Head :title="config.title" />
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="relative overflow-hidden rounded-3xl bg-[#121218] p-6 text-white shadow-xl md:p-8">
                <div class="absolute -right-12 -top-20 size-52 rounded-full bg-[#ec123c]/20 blur-3xl"></div>
                <div class="relative flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-4"><div class="rounded-2xl bg-[#ec123c] p-3"><component :is="config.icon" class="size-6" /></div><div><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#ff3b5d]">GORAKMANA Business</p><h1 class="mt-1 text-3xl font-black">{{ config.title }}</h1><p class="mt-1 text-sm text-zinc-400">{{ config.subtitle }}</p></div></div>
                    <Button class="gap-2 bg-[#ec123c] text-white hover:bg-[#c8102e]" @click="openCreate"><Plus class="size-4" /> Ajouter {{ section === 'clients' ? 'un client' : 'un service' }}</Button>
                </div>
            </div>

            <Card class="overflow-hidden border-0 shadow-sm"><CardHeader class="flex-row items-center justify-between border-b"><CardTitle>{{ records.length }} {{ config.title.toLowerCase() }}</CardTitle><div class="relative w-full max-w-xs"><Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" /><Input v-model="search" class="pl-9" placeholder="Rechercher…" /></div></CardHeader><CardContent class="p-0"><div class="overflow-x-auto">
                <table class="w-full text-sm"><thead class="bg-zinc-50 text-left text-xs font-bold uppercase tracking-wider text-zinc-500 dark:bg-zinc-900"><tr v-if="section === 'clients'"><th class="px-6 py-4">Code</th><th class="px-6 py-4">Client</th><th class="px-6 py-4">Contact</th><th class="px-6 py-4">Ville</th><th class="px-6 py-4">Statut</th><th class="px-6 py-4 text-right">Actions</th></tr><tr v-else><th class="px-6 py-4">Code</th><th class="px-6 py-4">Service</th><th class="px-6 py-4">Facturation</th><th class="px-6 py-4 text-right">Prix HT</th><th class="px-6 py-4">Statut</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y"><tr v-for="record in filteredRecords" :key="record.id" class="hover:bg-red-50/40 dark:hover:bg-red-950/10"><template v-if="section === 'clients'"><td class="px-6 py-4 font-bold text-[#ec123c]">{{ record.code }}</td><td class="px-6 py-4"><p class="font-semibold">{{ record.raison_sociale }}</p><p class="text-xs text-muted-foreground">ICE {{ record.ice || '—' }}</p></td><td class="px-6 py-4"><p>{{ record.email || '—' }}</p><p class="text-xs text-muted-foreground">{{ record.telephone }}</p></td><td class="px-6 py-4">{{ record.ville || '—' }}</td><td class="px-6 py-4"><Badge :class="record.status === 'active' ? 'bg-emerald-600' : ''">{{ record.status === 'active' ? 'Actif' : 'Inactif' }}</Badge></td></template><template v-else><td class="px-6 py-4 font-bold text-[#ec123c]">{{ record.code }}</td><td class="px-6 py-4"><p class="font-semibold">{{ record.name }}</p><p class="max-w-xs truncate text-xs text-muted-foreground">{{ record.description || '—' }}</p></td><td class="px-6 py-4">{{ record.billing_type }}</td><td class="px-6 py-4 text-right font-semibold">{{ Number(record.default_price_ht).toFixed(2) }} {{ record.currency }}</td><td class="px-6 py-4"><Badge :class="record.is_active ? 'bg-emerald-600' : ''">{{ record.is_active ? 'Actif' : 'Inactif' }}</Badge></td></template><td class="px-6 py-4"><div class="flex justify-end gap-1"><Button size="icon" variant="ghost" @click="openEdit(record)"><Pencil class="size-4" /></Button><Button size="icon" variant="ghost" class="text-red-500" @click="remove(record)"><Trash2 class="size-4" /></Button></div></td></tr><tr v-if="filteredRecords.length === 0"><td colspan="6" class="px-6 py-14 text-center text-muted-foreground">Aucun résultat.</td></tr></tbody>
                </table>
            </div></CardContent></Card>
        </div>

        <div v-if="showForm" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/65 p-4 backdrop-blur-sm md:p-8" @click.self="showForm = false"><Card class="w-full max-w-3xl"><CardHeader class="flex-row items-center justify-between border-b"><CardTitle>{{ editingId ? 'Modifier' : 'Ajouter' }} {{ section === 'clients' ? 'le client' : 'le service' }}</CardTitle><Button size="icon" variant="ghost" @click="showForm = false"><X class="size-5" /></Button></CardHeader><CardContent class="space-y-5 p-6">
            <div v-if="Object.keys(errors).length" class="rounded-xl bg-red-50 p-4 text-sm text-red-700"><p v-for="(messages, key) in errors" :key="key">{{ messages[0] }}</p></div>
            <template v-if="section === 'clients'"><div class="grid gap-4 md:grid-cols-2"><div class="space-y-2"><Label>Code client *</Label><Input v-model="form.code" placeholder="CLI-001" /></div><div class="space-y-2"><Label>Raison sociale *</Label><Input v-model="form.raison_sociale" /></div><div class="space-y-2"><Label>ICE</Label><Input v-model="form.ice" /></div><div class="space-y-2"><Label>Identifiant fiscal</Label><Input v-model="form.identifiant_fiscal" /></div><div class="space-y-2"><Label>Nom du contact</Label><Input v-model="form.contact_name" /></div><div class="space-y-2"><Label>Email</Label><Input v-model="form.email" type="email" /></div><div class="space-y-2"><Label>Téléphone</Label><Input v-model="form.telephone" /></div><div class="space-y-2"><Label>Ville</Label><Input v-model="form.ville" /></div><div class="space-y-2 md:col-span-2"><Label>Adresse</Label><Input v-model="form.adresse" /></div><div class="space-y-2"><Label>Pays</Label><Input v-model="form.pays" /></div><div class="space-y-2"><Label>Statut</Label><select v-model="form.status" class="h-9 w-full rounded-md border bg-background px-3 text-sm"><option value="active">Actif</option><option value="inactive">Inactif</option></select></div></div></template>
            <template v-else><div class="grid gap-4 md:grid-cols-2"><div class="space-y-2"><Label>Code service *</Label><Input v-model="form.code" placeholder="SRV-001" /></div><div class="space-y-2"><Label>Nom du service *</Label><Input v-model="form.name" /></div><div class="space-y-2 md:col-span-2"><Label>Description</Label><textarea v-model="form.description" rows="3" class="w-full rounded-md border bg-transparent px-3 py-2 text-sm"></textarea></div><div class="space-y-2"><Label>Type de facturation</Label><select v-model="form.billing_type" class="h-9 w-full rounded-md border bg-background px-3 text-sm"><option value="one_time">Une seule fois</option><option value="monthly">Mensuel</option><option value="quarterly">Trimestriel</option><option value="yearly">Annuel</option></select></div><div class="space-y-2"><Label>Prix HT</Label><Input v-model.number="form.default_price_ht" type="number" min="0" step="0.01" /></div><div class="space-y-2"><Label>TVA %</Label><Input v-model.number="form.default_tva_rate" type="number" min="0" max="100" step="0.01" /></div><div class="space-y-2"><Label>Statut</Label><select v-model="form.is_active" class="h-9 w-full rounded-md border bg-background px-3 text-sm"><option :value="true">Actif</option><option :value="false">Inactif</option></select></div></div></template>
        </CardContent><div class="flex justify-end gap-3 border-t p-5"><Button variant="outline" @click="showForm = false">Annuler</Button><Button class="bg-[#ec123c] text-white hover:bg-[#c8102e]" :disabled="submitting" @click="save">{{ submitting ? 'Enregistrement…' : 'Enregistrer' }}</Button></div></Card></div>
    </div>
</template>
