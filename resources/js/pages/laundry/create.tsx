import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, CheckCircle2, Shirt } from 'lucide-react';
import { useState, type FormEvent } from 'react';

interface ServiceType {
    id: number;
    name: string;
    price_per_kg: string | number;
}

interface CreateProps {
    serviceTypes: ServiceType[];
    expressFee: number;
}

export default function Create({ serviceTypes, expressFee }: CreateProps) {
    const [submitted, setSubmitted] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        customer_name: '',
        contact_number: '',
        pickup_date: '',
        express_service: false,
        items: [{ service_type_id: serviceTypes[0]?.id.toString() ?? '', weight_kg: '' }],
    });

    const getNestedError = (key: string) => Object.entries(errors).find(([errorKey]) => errorKey === key)?.[1];

    const submit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post('/orders', {
            onSuccess: () => {
                reset();
                setSubmitted(true);
            },
        });
    };

    return (
        <>
            <Head title="Create Laundry Order" />
            <div className="bg-muted/40 min-h-screen">
                <header className="bg-background border-b">
                    <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                        <Link href="/orders-list" className="flex items-center gap-3 text-sm font-semibold">
                            <span className="bg-primary text-primary-foreground flex size-9 items-center justify-center rounded-lg">
                                <Shirt aria-hidden="true" className="size-5" />
                            </span>
                            <span>Laundry operations</span>
                        </Link>
                        <Button asChild variant="ghost" size="sm">
                            <Link href="/orders-list">
                                <ArrowLeft aria-hidden="true" />
                                <span className="hidden sm:inline">Back to orders</span>
                                <span className="sm:hidden">Orders</span>
                            </Link>
                        </Button>
                    </div>
                </header>

                <main className="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-8 sm:px-6 md:py-12">
                    <div className="mx-auto w-full max-w-2xl space-y-2">
                        <p className="text-primary text-sm font-medium">ORDER MANAGEMENT</p>
                        <h1 className="text-3xl font-semibold tracking-tight">Create a laundry order</h1>
                        <p className="text-muted-foreground">Add the customer details and service information below.</p>
                    </div>

                    <Card className="mx-auto w-full max-w-2xl shadow-sm">
                        <CardHeader className="border-b">
                            <CardTitle className="text-lg">Order details</CardTitle>
                            <CardDescription>Fields marked below are required to place an order.</CardDescription>
                        </CardHeader>
                        <CardContent className="pt-6">
                            {submitted && (
                                <Alert className="mb-6 border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-100">
                                    <CheckCircle2 aria-hidden="true" className="size-4" />
                                    <AlertTitle>Order created</AlertTitle>
                                    <AlertDescription>Your laundry order was submitted successfully.</AlertDescription>
                                </Alert>
                            )}

                            <form onSubmit={submit} className="space-y-6">
                                <section className="space-y-4">
                                    <h2 className="text-sm font-semibold">Customer information</h2>
                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <div className="space-y-2 sm:col-span-2">
                                            <Label htmlFor="customer_name">Customer name</Label>
                                            <Input
                                                id="customer_name"
                                                autoComplete="name"
                                                value={data.customer_name}
                                                aria-invalid={Boolean(errors.customer_name)}
                                                onChange={(event) => {
                                                    setSubmitted(false);
                                                    setData('customer_name', event.target.value);
                                                }}
                                                placeholder="e.g. Alex Santos"
                                                required
                                            />
                                            {errors.customer_name && <p className="text-destructive text-sm">{errors.customer_name}</p>}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="contact_number">Contact number</Label>
                                            <Input
                                                id="contact_number"
                                                type="tel"
                                                autoComplete="tel"
                                                value={data.contact_number}
                                                aria-invalid={Boolean(errors.contact_number)}
                                                onChange={(event) => {
                                                    setSubmitted(false);
                                                    setData('contact_number', event.target.value);
                                                }}
                                                placeholder="09XXXXXXXXX"
                                                required
                                            />
                                            {errors.contact_number && <p className="text-destructive text-sm">{errors.contact_number}</p>}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="pickup_date">Pickup date</Label>
                                            <Input
                                                id="pickup_date"
                                                type="date"
                                                value={data.pickup_date}
                                                aria-invalid={Boolean(errors.pickup_date)}
                                                onChange={(event) => {
                                                    setSubmitted(false);
                                                    setData('pickup_date', event.target.value);
                                                }}
                                                required
                                            />
                                            {errors.pickup_date && <p className="text-destructive text-sm">{errors.pickup_date}</p>}
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3 rounded-lg border p-4">
                                        <Checkbox
                                            id="express_service"
                                            checked={data.express_service}
                                            onCheckedChange={(checked) => {
                                                setSubmitted(false);
                                                setData('express_service', checked === true);
                                            }}
                                        />
                                        <div className="space-y-1">
                                            <Label htmlFor="express_service" className="cursor-pointer">
                                                Express service
                                            </Label>
                                            <p className="text-muted-foreground text-sm">
                                                Ready 1 day after pickup with a{' '}
                                                {new Intl.NumberFormat('en-PH', {
                                                    style: 'currency',
                                                    currency: 'PHP',
                                                    maximumFractionDigits: 2,
                                                }).format(expressFee)}{' '}
                                                express fee.
                                            </p>
                                        </div>
                                    </div>
                                </section>

                                <section className="space-y-4 border-t pt-6">
                                    <div>
                                        <h2 className="text-sm font-semibold">Service details</h2>
                                        <p className="text-muted-foreground mt-1 text-sm">Choose a service and enter the laundry weight.</p>
                                    </div>
                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="service_type">Service type</Label>
                                            <Select
                                                value={data.items[0].service_type_id}
                                                onValueChange={(value) => {
                                                    setSubmitted(false);
                                                    setData('items', [{ ...data.items[0], service_type_id: value }]);
                                                }}
                                            >
                                                <SelectTrigger
                                                    id="service_type"
                                                    disabled={serviceTypes.length === 0}
                                                    aria-invalid={Boolean(getNestedError('items.0.service_type_id'))}
                                                >
                                                    <SelectValue placeholder="Select a service" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {serviceTypes.map((serviceType) => (
                                                        <SelectItem key={serviceType.id} value={serviceType.id.toString()}>
                                                            {serviceType.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            {serviceTypes.length === 0 && (
                                                <p className="text-muted-foreground text-sm">No services are configured yet.</p>
                                            )}
                                            {getNestedError('items.0.service_type_id') && (
                                                <p className="text-destructive text-sm">{getNestedError('items.0.service_type_id')}</p>
                                            )}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="weight_kg">Weight (kg)</Label>
                                            <Input
                                                id="weight_kg"
                                                type="number"
                                                min="0.1"
                                                step="0.1"
                                                inputMode="decimal"
                                                value={data.items[0].weight_kg}
                                                aria-invalid={Boolean(getNestedError('items.0.weight_kg'))}
                                                onChange={(event) => {
                                                    setSubmitted(false);
                                                    setData('items', [{ ...data.items[0], weight_kg: event.target.value }]);
                                                }}
                                                placeholder="e.g. 3.5"
                                                required
                                            />
                                            {getNestedError('items.0.weight_kg') && (
                                                <p className="text-destructive text-sm">{getNestedError('items.0.weight_kg')}</p>
                                            )}
                                        </div>
                                    </div>
                                </section>

                                <div className="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                                    <Button asChild type="button" variant="outline">
                                        <Link href="/orders-list">Cancel</Link>
                                    </Button>
                                    <Button type="submit" disabled={processing || serviceTypes.length === 0} className="sm:min-w-40">
                                        {processing ? 'Submitting order…' : 'Submit order'}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </main>
            </div>
        </>
    );
}
