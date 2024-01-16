@extends('layouts.admin')

@section('content-header', __('dashboard.title'))

@section('content')
    <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                  <h3>{{$orders_count}}</h3>
                <p>{{ __('dashboard.Orders_Count') }}</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                  <h3>{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</h3>
                <p>{{ __('dashboard.Income') }}</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>

                <p>{{ __('dashboard.Income_Today') }}</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{$customers_count}}</h3>

                <p>{{ __('dashboard.Customers_Count') }}</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{ route('customers.index') }}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
    </div>
</div>

    <div class="container-fluid">
        <div class="row">
            <!-- Existing code -->

            <!-- Modify the first line chart -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        Daily Rent Amount
                    </div>
                    <div class="card-body">
                        <canvas id="rentAmountChart"></canvas> <!-- Use a canvas element with the ID "rentAmountChart" -->
                    </div>
                </div>
            </div>

            <!-- Modify the second line chart -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        Product on Rent
                    </div>
                    <div class="card-body">
                        <canvas id="productOnRentChart"></canvas> <!-- Use a canvas element with the ID "productOnRentChart" -->
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
              <div class="card">
                  <div class="card-header">
                      Top 5 Most Rent Products
                  </div>
                  <div class="card-body">
                      <ul class="list-group">
                          @foreach($mostSellingProducts as $product)
                              <li class="list-group-item">{{ $product->name }}({{$product->total_quantity}} sales)</li>
                          @endforeach
                      </ul>
                  </div>
              </div>
          </div>

            <div class="col-lg-6 col-md-12">
              <div class="card">
                  <div class="card-header">
                      Top 5 Most Customers
                  </div>
                  <div class="card-body">
                      <ul class="list-group">
                          @foreach($topCustomers as $customer)
                              <li class="list-group-item">{{ $customer->first_name }} {{$customer->last_name}}</li>
                          @endforeach
                      </ul>
                  </div>
              </div>
          </div>

          
        </div>
    </div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Get the data from your backend - replace with actual data
    const dailyRentData = <?php echo json_encode($weeklyStatistics); ?>;

    const labels = Object.keys(dailyRentData);

    // Prepare the data for the first line chart
    const rentedAmounts = Object.values(dailyRentData).map((data) => data.rented_amount);

    // Create the first line chart
    const rentAmountChart = new Chart(document.getElementById('rentAmountChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Rented Amount',
                    data: rentedAmounts,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Prepare the data for the second line chart
    const productOnRent = Object.values(dailyRentData).map((data) => data.product_on_rent);

    // Create the second line chart
    const productOnRentChart = new Chart(document.getElementById('productOnRentChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Product on Rent',
                    data: productOnRent,
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection
