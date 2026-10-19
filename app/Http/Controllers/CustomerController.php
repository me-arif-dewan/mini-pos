<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\CustomerDTO;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Responses\CustomerFormResponse;
use App\Models\Customer;
use App\Pipelines\Filters\Customer\CustomerFilter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $queryBuilder =  Customer::withCount('completedOrders')
            ->withSum('completedOrders', 'grand_total')->latest()->orderBy('name');

        $customers = resolve(CustomerFilter::class)->getResults([
            'builder' => $queryBuilder,
            'params' => $request->query()
        ]);

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): CustomerFormResponse
    {
        return new CustomerFormResponse();
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create(CustomerDTO::fromRequest($request)->toArray());

        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function edit(Customer $customer): CustomerFormResponse
    {
        return new CustomerFormResponse($customer);
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update(CustomerDTO::fromRequest($request)->toArray());

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'Cannot delete a customer with existing orders.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
