import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { ClipboardList, Plus } from 'lucide-react';

interface Order {
    id: number;
    order_id?: number;
    customer?: { name?: string | null } | null;
    status?: string | null;
    grand_total?: string | number | null;
    total_amount?: string | number | null;
    created_at?: string | null;
}

interface IndexProps {
    orders: Order[];
}

const statusStyles: Record<string, string> = {
    pending: 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300',
    processing: 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-300',
    ready: 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300',
    completed: 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300',
    cancelled: 'border-destructive/20 bg-destructive/10 text-destructive',
};

function formatCurrency(amount: string | number | null | undefined) {
    const value = Number(amount ?? 0);

    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        maximumFractionDigits: 2,
    }).format(Number.isFinite(value) ? value : 0);
}

function formatDate(value: string | null | undefined) {
    if (!value) return '—';

    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? '—' : new Intl.DateTimeFormat('en-PH', { dateStyle: 'medium' }).format(date);
}

export default function Index({ orders }: IndexProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Orders List', href: '/orders-list' },
            ]}
        >
            <Head title="Laundry Orders" />

            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <p className="text-primary text-sm font-medium">ORDER MANAGEMENT</p>
                        <h1 className="text-3xl font-semibold tracking-tight">Laundry orders</h1>
                        <p className="text-muted-foreground text-sm">Review and manage orders placed by your customers.</p>
                    </div>
                    <Button asChild>
                        <Link href="/create-order">
                            <Plus aria-hidden="true" />
                            Create order
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader className="border-b px-5 py-4 sm:px-6">
                        <div className="flex items-center gap-3">
                            <div className="bg-primary/10 text-primary rounded-lg p-2">
                                <ClipboardList aria-hidden="true" className="size-5" />
                            </div>
                            <div>
                                <CardTitle className="text-base">All orders</CardTitle>
                                <CardDescription>
                                    {orders.length} {orders.length === 1 ? 'order' : 'orders'} in your system
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="p-0">
                        {orders.length === 0 ? (
                            <div className="flex flex-col items-center px-6 py-16 text-center">
                                <div className="bg-muted text-muted-foreground mb-4 rounded-full p-4">
                                    <ClipboardList aria-hidden="true" className="size-7" />
                                </div>
                                <h2 className="text-base font-semibold">No orders yet</h2>
                                <p className="text-muted-foreground mt-1 max-w-sm text-sm">
                                    New laundry orders will appear here. Create your first order to get started.
                                </p>
                                <Button asChild className="mt-5">
                                    <Link href="/create-order">
                                        <Plus aria-hidden="true" />
                                        Create your first order
                                    </Link>
                                </Button>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead className="pl-6">Order</TableHead>
                                            <TableHead>Customer</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead>Total</TableHead>
                                            <TableHead className="pr-6 text-right">Date created</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {orders.map((order) => {
                                            const status = order.status?.trim() || 'Pending';
                                            const statusClass = statusStyles[status.toLowerCase()] ?? 'border-muted bg-muted text-muted-foreground';

                                            return (
                                                <TableRow key={order.order_id ?? order.id}>
                                                    <TableCell className="pl-6 font-medium">#{order.order_id ?? order.id}</TableCell>
                                                    <TableCell>{order.customer?.name || 'Unknown customer'}</TableCell>
                                                    <TableCell>
                                                        <Badge variant="outline" className={statusClass}>
                                                            {status}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell className="tabular-nums">
                                                        {formatCurrency(order.grand_total ?? order.total_amount)}
                                                    </TableCell>
                                                    <TableCell className="text-muted-foreground pr-6 text-right">
                                                        {formatDate(order.created_at)}
                                                    </TableCell>
                                                </TableRow>
                                            );
                                        })}
                                    </TableBody>
                                </Table>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}
