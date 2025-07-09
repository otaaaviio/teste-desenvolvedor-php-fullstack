import type { DocumentType } from './DocumentType';
import type { SupplierAddress, CreateSupplierAddress } from './SupplierAddress';

type Supplier = {
    id: number;
    name: string;
    document: string;
    document_type: DocumentType;
    email?: string;
    phone?: string;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;
    address?: SupplierAddress;
};

type CreateSupplier = Omit<Supplier, 'id' | 'created_at' | 'updated_at' | 'deleted_at'> & {
    address: CreateSupplierAddress;
};

type UpdateSupplier = Pick<Supplier, 'id' | 'name' | 'email' | 'phone'>;

type SupplierList = Pick<Supplier, 'id' | 'name' | 'email' | 'phone' | 'created_at'>[];

export type { Supplier, CreateSupplier, UpdateSupplier, SupplierList };