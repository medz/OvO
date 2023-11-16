import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

class AllOf implements OvO {
  final Iterable<OvO> schemas;
  final String? message;

  const AllOf(this.schemas, [this.message]);

  @override
  Future handle(Context context, data) async {
    final exceptions = <Exception>[];
    for (final schema in schemas) {
      try {
        await schema.handle(context, data);
      } on Exception catch (e) {
        exceptions.add(e);
      }
    }

    if (exceptions.isEmpty) return data;
    throw OvOException(
      code: #all_of,
      message: message ?? 'All schemas must be valid',
      exceptions: exceptions,
    );
  }
}
