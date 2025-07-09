import type { CreateSupplier, Supplier, SupplierList, UpdateSupplier } from '@/types/Supplier';
import api from './api';
import type { PaginatedResponse } from '@/types/Pagination';

const getSuppliers = async (): Promise<PaginatedResponse<SupplierList>> => {
    const response = await api.get('/suppliers');
    return response.data.data;
};

const getSupplierById = async (id: number): Promise<Supplier> => {
    const response = await api.get(`/suppliers/${id}`);
    return response.data.data;
};

const createSupplier = async (supplierData: CreateSupplier): Promise<Supplier> => {
    const response = await api.post('/suppliers', supplierData);
    return response.data.data;
};

const updateSupplier = async (id: number, supplierData: UpdateSupplier): Promise<Supplier> => {
    const response = await api.put(`/suppliers/${id}`, supplierData);
    return response.data.data;
};

const deleteSupplier = async (id: number): Promise<void> => {
    await api.delete(`/suppliers/${id}`);
};

const getSupplierByCnpj = async (cnpj: string): Promise<Supplier> => {
    const response = await api.get(`/suppliers/cnpj/${cnpj}`);
    return response.data.data;
};

export { getSuppliers, getSupplierById, createSupplier, updateSupplier, deleteSupplier, getSupplierByCnpj };