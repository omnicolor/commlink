<div class="row my-4"></div>
<div class="row my-4"></div>

<div class="d-flex fixed-bottom justify-content-center">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            @if (null === $previousStep)
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <button aria-label="Previous" class="page-link"
                        name="nav" type="submit" value="prev">
                        <span aria-hidden="true">&laquo;</span>
                        Previous: {{ ucfirst($previousStep) }}
                    </button>
                </li>
            @endif
            <li class="page-item active">
                <a aria-label="Current" class="page-link" href="#">
                    {{ ucfirst($currentStep) }}
                </a>
            </li>
            @if (null === $nextStep)
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">
                        <span class="sr-only">Next</span>
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <button aria-label="Next" class="page-link" name="nav"
                        type="submit" value="next">
                        Next: {{ ucfirst($nextStep) }}
                        <span aria-hidden="true">&raquo;</span>
                    </button>
                </li>
            @endif
        </ul>
    </nav>
</div>
