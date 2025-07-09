type SupplierAddress = {
    id: number;
    supplier_id: number;
    street: string;
    number?: string | null;
    complement?: string | null;
    neighborhood?: string | null;
    city: string;
    state: string;
    zip_code: string;
    created_at: string;
}

type CreateSupplierAddress = Omit<SupplierAddress, 'id' | 'supplier_id' | 'created_at'>;

export type { SupplierAddress, CreateSupplierAddress };