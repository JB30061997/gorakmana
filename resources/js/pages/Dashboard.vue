<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Banknote, FileCheck2, FilePlus2, FileText, PackageCheck, Pencil, Plus, Printer, Trash2, Users, X } from '@lucide/vue';
import { computed, reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import { invoices as invoicesRoute, orders as ordersRoute, quotes as quotesRoute } from '@/routes/commercial';

type Client = { id: number; code: string; raison_sociale: string };
type Service = { id: number; code: string; name: string; default_price_ht: string; default_tva_rate: string };
type DocumentItem = { id?: number; service_id: number | null; description: string; quantity: number; unit_price_ht: number; tva_rate: number; amount_ht?: string; amount_tva?: string; amount_ttc?: string };
type Quote = { id: number; client_id: number; reference: string; order_date: string; status: string; notes: string | null; total_ht: string; total_tva: string; total_ttc: string; currency: string; client: { raison_sociale: string }; items: DocumentItem[]; invoices: { id: number; invoice_number: string; status: string }[] };
type Invoice = { id: number; invoice_number: string; invoice_date: string; due_date: string | null; status: string; client_name: string; client_ice: string | null; client_if: string | null; client_address: string | null; total_ht: string; total_tva: string; total_ttc: string; currency: string; notes: string | null; client: { raison_sociale: string }; items: DocumentItem[] };

const props = defineProps<{
    section: 'quotes' | 'orders' | 'invoices';
    stats: { clients: number; quotes: number; invoices: number; revenue: number | string };
    clients: Client[];
    services: Service[];
    quotes: Quote[];
    orders: Quote[];
    invoices: Invoice[];
}>();

defineOptions({ layout: { breadcrumbs: [{ title: 'Gestion commerciale', href: dashboard() }] } });

const showQuoteForm = ref(false);
const editingQuoteId = ref<number | null>(null);
const submitting = ref(false);
const activeTab = ref<'quotes' | 'orders' | 'invoices'>(props.section);
const errors = ref<Record<string, string[]>>({});
const today = new Date().toISOString().slice(0, 10);

function emptyItem(): DocumentItem {
    return { service_id: null, description: '', quantity: 1, unit_price_ht: 0, tva_rate: 20 };
}

const quoteForm = reactive({ client_id: '', order_date: today, status: 'draft', notes: '', items: [emptyItem()] as DocumentItem[] });
const totals = computed(() => {
    const ht = quoteForm.items.reduce((sum, item) => sum + item.quantity * item.unit_price_ht, 0);
    const tva = quoteForm.items.reduce((sum, item) => sum + item.quantity * item.unit_price_ht * (item.tva_rate / 100), 0);
    return { ht, tva, ttc: ht + tva };
});

function money(value: string | number): string {
    return new Intl.NumberFormat('fr-MA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value));
}

function selectService(item: DocumentItem): void {
    const service = props.services.find((candidate) => candidate.id === Number(item.service_id));
    if (!service) return;
    item.description = service.name;
    item.unit_price_ht = Number(service.default_price_ht);
    item.tva_rate = Number(service.default_tva_rate);
}

function removeItem(index: number): void {
    if (quoteForm.items.length > 1) quoteForm.items.splice(index, 1);
}

function resetForm(): void {
    Object.assign(quoteForm, { client_id: '', order_date: today, status: 'draft', notes: '', items: [emptyItem()] });
    errors.value = {};
    editingQuoteId.value = null;
}

function openEditQuote(quote: Quote): void {
    editingQuoteId.value = quote.id;
    quoteForm.client_id = String(quote.client_id);
    quoteForm.order_date = quote.order_date.slice(0, 10);
    quoteForm.status = 'draft';
    quoteForm.notes = quote.notes ?? '';
    quoteForm.items = quote.items.map((item) => ({
        service_id: item.service_id,
        description: item.description,
        quantity: Number(item.quantity),
        unit_price_ht: Number(item.unit_price_ht),
        tva_rate: Number(item.tva_rate),
    }));
    errors.value = {};
    showQuoteForm.value = true;
}

function csrfToken(): string {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

async function submitQuote(): Promise<void> {
    submitting.value = true;
    errors.value = {};
    try {
        const response = await fetch(editingQuoteId.value ? `/admin/orders/${editingQuoteId.value}` : '/admin/orders', {
            method: editingQuoteId.value ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify(quoteForm),
        });
        const data = await response.json();
        if (!response.ok) {
            errors.value = data.errors ?? { form: [data.message ?? 'Une erreur est survenue.'] };
            return;
        }
        toast.success(data.message);
        showQuoteForm.value = false;
        resetForm();
        router.reload();
    } catch {
        toast.error('Impossible de contacter le serveur.');
    } finally {
        submitting.value = false;
    }
}

async function generateInvoice(quote: Quote): Promise<void> {
    if (quote.invoices.length > 0) return;
    try {
        const response = await fetch(`/admin/orders/${quote.id}/invoice`, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        });
        const data = await response.json();
        if (!response.ok) {
            toast.error(data.message ?? 'Impossible de générer la facture.');
            return;
        }
        toast.success(data.message);
        router.visit(invoicesRoute());
    } catch {
        toast.error('Impossible de contacter le serveur.');
    }
}

async function validateQuote(quote: Quote): Promise<void> {
    try {
        const response = await fetch(`/admin/orders/${quote.id}/validate`, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        });
        const data = await response.json();
        if (!response.ok) {
            toast.error(data.message ?? 'Impossible de valider le devis.');
            return;
        }
        toast.success(data.message);
        router.visit(ordersRoute());
    } catch {
        toast.error('Impossible de contacter le serveur.');
    }
}

function printInvoice(invoice: Invoice): void {
    window.open(`/documents/factures/${invoice.id}.pdf`, '_blank');
    return;

    printDocument({
        type: 'FACTURE',
        reference: invoice.invoice_number,
        date: invoice.invoice_date,
        dueDate: invoice.due_date,
        clientName: invoice.client_name,
        clientDetails: [invoice.client_address, invoice.client_ice ? `ICE : ${invoice.client_ice}` : null, invoice.client_if ? `IF : ${invoice.client_if}` : null].filter(Boolean).join('<br>'),
        items: invoice.items,
        totalHt: invoice.total_ht,
        totalTva: invoice.total_tva,
        totalTtc: invoice.total_ttc,
        notes: invoice.notes,
    });
}

function printQuote(quote: Quote, type: 'DEVIS' | 'COMMANDE' = 'DEVIS'): void {
    const documentPath = type === 'COMMANDE' ? 'commandes' : 'devis';
    window.open(`/documents/${documentPath}/${quote.id}.pdf`, '_blank');
    return;

    printDocument({
        type,
        reference: type === 'DEVIS' ? quote.reference.replace('CMD-', 'DEV-') : quote.reference,
        date: quote.order_date,
        clientName: quote.client.raison_sociale,
        clientDetails: '',
        items: quote.items,
        totalHt: quote.total_ht,
        totalTva: quote.total_tva,
        totalTtc: quote.total_ttc,
        notes: quote.notes,
    });
}

function printDocument(document: {
    type: 'DEVIS' | 'COMMANDE' | 'FACTURE';
    reference: string;
    date: string;
    dueDate?: string | null;
    clientName: string;
    clientDetails: string;
    items: DocumentItem[];
    totalHt: string;
    totalTva: string;
    totalTtc: string;
    notes?: string | null;
}): void {
    const rows = document.items.map((item, index) => {
        const amountHt = item.amount_ht ?? Number(item.quantity) * Number(item.unit_price_ht);
        const amountTtc = item.amount_ttc ?? Number(amountHt) * (1 + Number(item.tva_rate) / 100);
        return `<tr><td>${String(index + 1).padStart(2, '0')}</td><td><strong>${item.description}</strong></td><td>${money(item.quantity)}</td><td class="money">${money(item.unit_price_ht)} MAD</td><td class="money">${money(item.tva_rate)}%</td><td class="money">${money(amountTtc)} MAD</td></tr>`;
    }).join('');
    const printable = window.open('', '_blank', 'width=900,height=700');
    if (!printable) {
        toast.error(`Autorisez les fenêtres pop-up pour imprimer ${document.type.toLowerCase()}.`);
        return;
    }
    const recipientLabel = document.type === 'FACTURE' ? 'Facturé à' : document.type === 'COMMANDE' ? 'Commande de' : 'Proposé à';
    const notice = document.notes || (document.type === 'DEVIS' ? 'Ce devis est valable 30 jours à compter de sa date d’émission. Les montants sont exprimés en dirhams marocains (MAD).' : document.type === 'COMMANDE' ? 'La présente commande confirme les prestations détaillées ci-dessus. Les montants sont exprimés en dirhams marocains (MAD).' : 'La présente facture reprend les prestations indiquées ci-dessus. Tous les montants sont exprimés en dirhams marocains (MAD).');
    const dueDate = document.dueDate ? `<div>Échéance : ${formatDate(document.dueDate)}</div>` : '';
    printable.document.write(`<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>${document.reference}</title><style>@page{size:A4;margin:14mm 14mm 18mm}*{box-sizing:border-box}body{margin:0;color:#172033;font-family:Arial,Helvetica,sans-serif;font-size:9px}.page-border{position:fixed;z-index:-1;top:-7mm;right:-7mm;bottom:-11mm;left:-7mm;border:1.2px dashed #ec123c;border-radius:4mm}.header{padding:1mm 2mm 6mm;border-bottom:1.5px solid #ec123c;margin-bottom:7mm}.header-table,.info,.totals{width:100%;border-collapse:collapse}.logo-mark{display:inline-flex;width:17mm;height:17mm;align-items:center;justify-content:center;border-radius:4mm;background:#ec123c;color:#fff;font-size:28px;font-weight:900;vertical-align:middle}.logo-name{display:inline-block;margin-left:4mm;vertical-align:middle;color:#17171d;font-size:20px;font-weight:900;letter-spacing:.5px}.logo-name span{color:#ec123c}.logo-sub{display:block;margin-top:1mm;color:#64748b;font-size:7px;font-weight:600;letter-spacing:1.5px}.brand{text-align:right}.brand h1{margin:0;color:#ec123c;font-size:23px;letter-spacing:.5px}.brand p{margin:4px 0 0;color:#64748b;font-size:8px;letter-spacing:1.5px}.info{margin-bottom:8mm}.info td{width:50%;padding:5mm;border:1px solid #e5e7eb;vertical-align:top;background:#fff}.info td:first-child{border-left:3px solid #ec123c}.label{display:block;margin-bottom:3px;color:#94a3b8;font-size:7px;font-weight:bold;text-transform:uppercase;letter-spacing:1px}.recipient{font-size:13px;font-weight:bold}.contact{margin-top:4px;color:#64748b;line-height:1.6}.meta{text-align:right;line-height:1.8}.meta strong{color:#ec123c}.section-title{margin:0 0 3mm;padding-left:2mm;border-left:2px solid #ec123c;font-size:10px;font-weight:bold}.lines{width:100%;border-collapse:collapse}.lines th{padding:3mm 2mm;background:#172033;color:#fff;text-align:left;font-size:7px;text-transform:uppercase;letter-spacing:.5px}.lines th:first-child{border-radius:2mm 0 0 0}.lines th:last-child{border-radius:0 2mm 0 0}.lines td{padding:3.2mm 2mm;border-bottom:1px solid #e5e7eb;vertical-align:top}.lines tbody tr:nth-child(even){background:#f8fafc}.money{text-align:right!important;white-space:nowrap;font-weight:bold}.totals{width:82mm;margin:8mm 0 0 auto}.totals td{padding:2.7mm 3mm;border-bottom:1px solid #e5e7eb}.totals td:last-child{text-align:right;font-weight:bold;white-space:nowrap}.totals .grand td{padding-top:4mm;border-top:2px solid #ec123c;border-bottom:0;color:#ec123c;font-size:13px}.notice{margin-top:8mm;padding:4mm;border:1px dashed #f2a8b6;border-radius:3mm;background:#fff8f8;color:#64748b;line-height:1.6}.footer{position:fixed;bottom:-9mm;left:0;right:0;color:#94a3b8;font-size:7px;text-align:center}@media print{body{-webkit-print-color-adjust:exact;print-color-adjust:exact}}</style></head><body><div class="page-border"></div><div class="header"><table class="header-table"><tr><td><span class="logo-mark">G</span><span class="logo-name">GORA<span>KMANA</span><small class="logo-sub">SOLUTIONS DIGITALES</small></span></td><td class="brand"><h1>${document.type}</h1><p>GORAKMANA BUSINESS</p></td></tr></table></div><table class="info"><tr><td><span class="label">${recipientLabel}</span><div class="recipient">${document.clientName}</div><div class="contact">${document.clientDetails || 'Coordonnées client enregistrées dans GORAKMANA Business'}</div></td><td class="meta"><span class="label">Informations ${document.type.toLowerCase()}</span><div><strong>N° ${document.reference}</strong></div><div>Date : ${formatDate(document.date)}</div>${dueDate}<div>Devise : MAD</div></td></tr></table><div class="section-title">DÉTAIL DES PRESTATIONS</div><table class="lines"><thead><tr><th>#</th><th>Service / Désignation</th><th>Qté</th><th class="money">Prix HT</th><th class="money">TVA</th><th class="money">Montant TTC</th></tr></thead><tbody>${rows}</tbody></table><table class="totals"><tr><td>Total HT</td><td>${money(document.totalHt)} MAD</td></tr><tr><td>TVA</td><td>${money(document.totalTva)} MAD</td></tr><tr class="grand"><td>Total TTC</td><td>${money(document.totalTtc)} MAD</td></tr></table><div class="notice">${notice}</div><div class="footer">GORAKMANA · Solutions digitales & technologiques · Document généré le ${new Intl.DateTimeFormat('fr-MA', { dateStyle: 'short', timeStyle: 'short' }).format(new Date())}</div><script>window.onload=()=>window.print()<\/script></body></html>`);
    printable.document.close();
}

function formatDate(value: string): string {
    return new Intl.DateTimeFormat('fr-MA', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(value));
}

const statusLabels: Record<string, string> = { draft: 'Brouillon', validated: 'Validé', issued: 'Émise', partially_paid: 'Partiellement payée', paid: 'Payée', overdue: 'En retard', cancelled: 'Annulée' };
</script>

<template>
    <div class="min-h-full bg-slate-50/70 p-4 md:p-6 dark:bg-background">
        <Head title="Gestion commerciale" />
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="relative overflow-hidden rounded-3xl bg-[#121218] p-6 text-white shadow-xl md:p-8">
                <div class="absolute -right-12 -top-20 size-52 rounded-full bg-[#ec123c]/20 blur-3xl"></div>
                <div class="relative flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div><p class="text-xs font-bold uppercase tracking-[0.22em] text-[#ff3b5d]">GORAKMANA Business</p><h1 class="mt-1 text-3xl font-black md:text-4xl">{{ section === 'quotes' ? 'Devis' : section === 'orders' ? 'Commandes' : 'Factures' }}</h1><p class="mt-2 text-sm text-zinc-400">{{ section === 'quotes' ? 'Préparez et validez vos propositions commerciales.' : section === 'orders' ? 'Suivez les devis acceptés et générez leurs factures.' : 'Consultez, imprimez et exportez vos factures.' }}</p></div>
                    <Button v-if="section === 'quotes'" class="gap-2 bg-[#ec123c] text-white hover:bg-[#c8102e]" @click="showQuoteForm = true"><FilePlus2 class="size-4" /> Nouveau devis</Button>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Card v-for="card in [{ label: 'Clients actifs', value: stats.clients, icon: Users }, { label: 'Devis', value: stats.quotes, icon: FileText }, { label: 'Factures', value: stats.invoices, icon: FileCheck2 }, { label: 'Chiffre facturé', value: `${money(stats.revenue)} MAD`, icon: Banknote }]" :key="card.label" class="border-0 shadow-sm">
                    <CardContent class="flex items-center justify-between p-5"><div><p class="text-sm text-muted-foreground">{{ card.label }}</p><p class="mt-1 text-2xl font-bold">{{ card.value }}</p></div><div class="rounded-xl bg-red-50 p-3 text-[#ec123c] dark:bg-red-950"><component :is="card.icon" class="size-5" /></div></CardContent>
                </Card>
            </div>

            <Card class="border-0 shadow-sm">
                <CardHeader class="border-b pb-0"><div class="flex gap-6"><Link v-for="tab in [{ id: 'quotes', label: 'Devis', href: quotesRoute() }, { id: 'orders', label: 'Commandes', href: ordersRoute() }, { id: 'invoices', label: 'Factures', href: invoicesRoute() }]" :key="tab.id" :href="tab.href" class="border-b-2 px-1 pb-4 text-sm font-semibold transition-colors" :class="activeTab === tab.id ? 'border-[#ec123c] text-[#ec123c]' : 'border-transparent text-muted-foreground'">{{ tab.label }}</Link></div></CardHeader>
                <CardContent class="p-0"><div class="overflow-x-auto">
                    <table v-if="activeTab === 'quotes'" class="w-full text-sm"><thead class="bg-slate-50 text-left text-xs uppercase text-muted-foreground dark:bg-muted/30"><tr><th class="px-5 py-3">Référence</th><th class="px-5 py-3">Client</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Statut</th><th class="px-5 py-3 text-right">Total TTC</th><th class="px-5 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">
                        <tr v-for="quote in quotes" :key="quote.id" class="hover:bg-muted/30"><td class="px-5 py-4 font-semibold text-[#ec123c]">{{ quote.reference.replace('CMD-', 'DEV-') }}</td><td class="px-5 py-4">{{ quote.client.raison_sociale }}</td><td class="px-5 py-4 text-muted-foreground">{{ formatDate(quote.order_date) }}</td><td class="px-5 py-4"><Badge variant="secondary">{{ statusLabels[quote.status] ?? quote.status }}</Badge></td><td class="px-5 py-4 text-right font-semibold">{{ money(quote.total_ttc) }} {{ quote.currency }}</td><td class="px-5 py-4"><div class="flex justify-end gap-2"><Button size="sm" variant="outline" class="gap-2" @click="printQuote(quote)"><Printer class="size-4" /> Imprimer</Button><Button size="sm" variant="outline" class="gap-2" @click="openEditQuote(quote)"><Pencil class="size-4" /> Modifier</Button><Button size="sm" class="gap-2 bg-[#ec123c] text-white hover:bg-[#c8102e]" @click="validateQuote(quote)"><PackageCheck class="size-4" /> Commander</Button></div></td></tr>
                        <tr v-if="quotes.length === 0"><td colspan="6" class="px-5 py-12 text-center text-muted-foreground">Aucun devis. Créez votre premier devis.</td></tr>
                    </tbody></table>
                    <table v-else-if="activeTab === 'orders'" class="w-full text-sm"><thead class="bg-slate-50 text-left text-xs uppercase text-muted-foreground dark:bg-muted/30"><tr><th class="px-5 py-3">Commande</th><th class="px-5 py-3">Client</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Statut</th><th class="px-5 py-3 text-right">Total TTC</th><th class="px-5 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-muted/30"><td class="px-5 py-4 font-semibold text-[#ec123c]">{{ order.reference }}</td><td class="px-5 py-4">{{ order.client.raison_sociale }}</td><td class="px-5 py-4 text-muted-foreground">{{ formatDate(order.order_date) }}</td><td class="px-5 py-4"><Badge variant="secondary">{{ statusLabels[order.status] ?? order.status }}</Badge></td><td class="px-5 py-4 text-right font-semibold">{{ money(order.total_ttc) }} {{ order.currency }}</td><td class="px-5 py-4"><div class="flex justify-end gap-2"><Button size="sm" variant="outline" class="gap-2" @click="printQuote(order, 'COMMANDE')"><Printer class="size-4" /> Imprimer</Button><Button v-if="order.invoices.length === 0" size="sm" class="gap-2 bg-[#ec123c] text-white hover:bg-[#c8102e]" @click="generateInvoice(order)"><FileCheck2 class="size-4" /> Générer facture</Button><Badge v-else class="bg-emerald-600">{{ order.invoices[0].invoice_number }}</Badge></div></td></tr>
                        <tr v-if="orders.length === 0"><td colspan="6" class="px-5 py-12 text-center text-muted-foreground">Aucune commande. Validez d’abord un devis.</td></tr>
                    </tbody></table>
                    <table v-else class="w-full text-sm"><thead class="bg-slate-50 text-left text-xs uppercase text-muted-foreground dark:bg-muted/30"><tr><th class="px-5 py-3">N° facture</th><th class="px-5 py-3">Client</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Statut</th><th class="px-5 py-3 text-right">Total TTC</th><th class="px-5 py-3 text-right">Action</th></tr></thead><tbody class="divide-y">
                        <tr v-for="invoice in invoices" :key="invoice.id" class="hover:bg-muted/30"><td class="px-5 py-4 font-semibold">{{ invoice.invoice_number }}</td><td class="px-5 py-4">{{ invoice.client_name }}</td><td class="px-5 py-4 text-muted-foreground">{{ formatDate(invoice.invoice_date) }}</td><td class="px-5 py-4"><Badge class="bg-emerald-600">{{ statusLabels[invoice.status] ?? invoice.status }}</Badge></td><td class="px-5 py-4 text-right font-semibold">{{ money(invoice.total_ttc) }} {{ invoice.currency }}</td><td class="px-5 py-4 text-right"><Button size="sm" variant="outline" class="gap-2" @click="printInvoice(invoice)"><Printer class="size-4" /> Imprimer / PDF</Button></td></tr>
                        <tr v-if="invoices.length === 0"><td colspan="6" class="px-5 py-12 text-center text-muted-foreground">Aucune facture générée.</td></tr>
                    </tbody></table>
                </div></CardContent>
            </Card>
        </div>

        <div v-if="showQuoteForm" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/55 p-4 backdrop-blur-sm md:p-8" @click.self="showQuoteForm = false">
            <Card class="w-full max-w-5xl shadow-2xl"><CardHeader class="flex-row items-center justify-between border-b"><div><CardTitle>{{ editingQuoteId ? 'Modifier le devis' : 'Nouveau devis' }}</CardTitle><p class="mt-1 text-sm text-muted-foreground">Renseignez le client et les prestations.</p></div><Button variant="ghost" size="icon" @click="showQuoteForm = false"><X class="size-5" /></Button></CardHeader>
                <CardContent class="space-y-6 p-5 md:p-6">
                    <div v-if="Object.keys(errors).length" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><p v-for="(messages, key) in errors" :key="key">{{ messages[0] }}</p></div>
                    <div class="grid gap-4 md:grid-cols-3"><div class="space-y-2 md:col-span-2"><Label>Client</Label><select v-model="quoteForm.client_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none focus:ring-2 focus:ring-ring"><option value="" disabled>Sélectionner un client</option><option v-for="client in clients" :key="client.id" :value="client.id">{{ client.code }} — {{ client.raison_sociale }}</option></select></div><div class="space-y-2"><Label>Date du devis</Label><Input v-model="quoteForm.order_date" type="date" /></div></div>
                    <div class="space-y-3"><div class="flex items-center justify-between"><h3 class="font-semibold">Prestations</h3><Button size="sm" variant="outline" class="gap-2" @click="quoteForm.items.push(emptyItem())"><Plus class="size-4" /> Ajouter une ligne</Button></div>
                        <div v-for="(item, index) in quoteForm.items" :key="index" class="grid gap-3 rounded-xl border bg-muted/20 p-4 md:grid-cols-12 md:items-end">
                            <div class="space-y-2 md:col-span-3"><Label>Service</Label><select v-model="item.service_id" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" @change="selectService(item)"><option :value="null" disabled>Choisir</option><option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option></select></div>
                            <div class="space-y-2 md:col-span-3"><Label>Description</Label><Input v-model="item.description" /></div><div class="space-y-2 md:col-span-1"><Label>Qté</Label><Input v-model.number="item.quantity" type="number" min="0.01" step="0.01" /></div><div class="space-y-2 md:col-span-2"><Label>Prix HT</Label><Input v-model.number="item.unit_price_ht" type="number" min="0" step="0.01" /></div><div class="space-y-2 md:col-span-1"><Label>TVA %</Label><Input v-model.number="item.tva_rate" type="number" min="0" max="100" step="0.01" /></div><div class="md:col-span-1"><p class="mb-2 text-xs text-muted-foreground">TTC</p><p class="h-9 pt-2 text-sm font-semibold">{{ money(item.quantity * item.unit_price_ht * (1 + item.tva_rate / 100)) }}</p></div><Button variant="ghost" size="icon" class="text-red-500 md:col-span-1" :disabled="quoteForm.items.length === 1" @click="removeItem(index)"><Trash2 class="size-4" /></Button>
                        </div>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2"><div class="space-y-2"><Label>Notes</Label><textarea v-model="quoteForm.notes" rows="4" class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm" placeholder="Conditions, délai de livraison…"></textarea></div><div class="rounded-xl bg-slate-900 p-5 text-white"><div class="space-y-2 text-sm"><p class="flex justify-between"><span class="text-slate-300">Total HT</span><strong>{{ money(totals.ht) }} MAD</strong></p><p class="flex justify-between"><span class="text-slate-300">TVA</span><strong>{{ money(totals.tva) }} MAD</strong></p><p class="flex justify-between border-t border-slate-700 pt-3 text-lg"><span>Total TTC</span><strong class="text-emerald-400">{{ money(totals.ttc) }} MAD</strong></p></div></div></div>
                </CardContent><div class="flex justify-end gap-3 border-t p-5"><Button variant="outline" @click="showQuoteForm = false">Annuler</Button><Button class="bg-[#ec123c] text-white hover:bg-[#c8102e]" :disabled="submitting" @click="submitQuote">{{ submitting ? 'Enregistrement…' : editingQuoteId ? 'Enregistrer les modifications' : 'Enregistrer le devis' }}</Button></div>
            </Card>
        </div>
    </div>
</template>
