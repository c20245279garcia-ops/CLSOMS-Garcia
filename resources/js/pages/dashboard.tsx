import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowRight, ClipboardList, Plus, Shirt } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <main className="flex flex-1 flex-col gap-8 p-4 md:p-8">
                <section className="bg-primary text-primary-foreground relative overflow-hidden rounded-2xl px-6 py-8 shadow-sm md:px-10 md:py-12">
                    <div className="relative z-10 max-w-2xl">
                        <div className="bg-primary-foreground/10 mb-4 flex size-11 items-center justify-center rounded-xl">
                            <Shirt aria-hidden="true" className="size-5" />
                        </div>
                        <p className="text-primary-foreground/70 mb-2 text-sm font-medium">LAUNDRY OPERATIONS</p>
                        <h1 className="text-3xl font-semibold tracking-tight md:text-4xl">Welcome to your workspace</h1>
                        <p className="text-primary-foreground/75 mt-3 max-w-xl text-sm leading-6 md:text-base">
                            Manage customer orders and keep every laundry service moving smoothly.
                        </p>
                        <div className="mt-6 flex flex-wrap gap-3">
                            <Button asChild variant="secondary">
                                <Link href="/create-order">
                                    <Plus aria-hidden="true" />
                                    Create an order
                                </Link>
                            </Button>
                            <Button
                                asChild
                                variant="ghost"
                                className="text-primary-foreground hover:bg-primary-foreground/10 hover:text-primary-foreground"
                            >
                                <Link href="/orders-list">
                                    View all orders
                                    <ArrowRight aria-hidden="true" />
                                </Link>
                            </Button>
                        </div>
                    </div>
                    <Shirt
                        aria-hidden="true"
                        className="text-primary-foreground/[0.06] absolute -right-8 -bottom-16 size-72 md:-right-2 md:size-96"
                    />
                </section>

                <section aria-labelledby="quick-actions-heading" className="space-y-4">
                    <div>
                        <h2 id="quick-actions-heading" className="text-xl font-semibold tracking-tight">
                            Quick actions
                        </h2>
                        <p className="text-muted-foreground mt-1 text-sm">Jump into the most common tasks.</p>
                    </div>
                    <div className="grid gap-4 md:grid-cols-2">
                        <Card className="hover:border-primary/30 transition-colors">
                            <CardHeader className="flex flex-row items-start justify-between space-y-0">
                                <div className="space-y-1.5">
                                    <CardTitle className="text-base">New laundry order</CardTitle>
                                    <CardDescription>Record a customer and the service they need.</CardDescription>
                                </div>
                                <div className="bg-primary/10 text-primary rounded-lg p-2">
                                    <Plus aria-hidden="true" className="size-5" />
                                </div>
                            </CardHeader>
                            <CardContent>
                                <Button asChild variant="outline" size="sm">
                                    <Link href="/create-order">
                                        Start an order <ArrowRight aria-hidden="true" />
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                        <Card className="hover:border-primary/30 transition-colors">
                            <CardHeader className="flex flex-row items-start justify-between space-y-0">
                                <div className="space-y-1.5">
                                    <CardTitle className="text-base">Order list</CardTitle>
                                    <CardDescription>Review the latest laundry orders and their status.</CardDescription>
                                </div>
                                <div className="bg-primary/10 text-primary rounded-lg p-2">
                                    <ClipboardList aria-hidden="true" className="size-5" />
                                </div>
                            </CardHeader>
                            <CardContent>
                                <Button asChild variant="outline" size="sm">
                                    <Link href="/orders-list">
                                        Browse orders <ArrowRight aria-hidden="true" />
                                    </Link>
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </section>
            </main>
        </AppLayout>
    );
}
